<?php

require_once __DIR__ . '/../config/Database.php';

class Venta
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function registrarVentaBoleteria(
        int $idUsuario,
        int $idFuncion,
        array $butacas,
        float $total,
        string $metodoPago
    ): array|false {
        if (empty($butacas) || $total <= 0 || $idFuncion <= 0) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // 1. Validar que ninguna de las butacas haya sido vendida en el ínterin (prevención de concurrencia)
            $placeholders = implode(',', array_fill(0, count($butacas), '?'));
            $sqlCheck = "SELECT fila_columna FROM boletos 
                         WHERE id_funcion = ? AND fila_columna IN ($placeholders) 
                         FOR UPDATE";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute(array_merge([$idFuncion], $butacas));
            $ocupadas = $stmtCheck->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($ocupadas)) {
                $this->db->rollBack();
                return [
                    'error' => 'conflicto',
                    'butacas_conflicto' => $ocupadas
                ];
            }

            // 2. Insertar en tabla ventas
            $sqlVenta = "INSERT INTO ventas (id_usuario, total, metodo_pago, fecha_hora, tipo_venta)
                         VALUES (:id_usuario, :total, :metodo_pago, NOW(), 'boleteria')";
            $stmtVenta = $this->db->prepare($sqlVenta);
            $stmtVenta->execute([
                ':id_usuario'   => $idUsuario,
                ':total'        => $total,
                ':metodo_pago'  => $metodoPago
            ]);
            $idVenta = (int)$this->db->lastInsertId();

            // 3. Insertar cada butaca en tabla boletos
            $sqlBoleto = "INSERT INTO boletos (id_funcion, id_venta, fila_columna)
                          VALUES (:id_funcion, :id_venta, :fila_columna)";
            $stmtBoleto = $this->db->prepare($sqlBoleto);

            foreach ($butacas as $asiento) {
                $stmtBoleto->execute([
                    ':id_funcion'    => $idFuncion,
                    ':id_venta'      => $idVenta,
                    ':fila_columna'  => trim($asiento)
                ]);
            }

            $this->db->commit();

            return [
                'exito'    => true,
                'id_venta' => $idVenta,
                'total'    => $total,
                'metodo'   => $metodoPago,
                'butacas'  => $butacas
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function obtenerVentaPorId(int $idVenta): array|false
    {
        $sql = "SELECT v.id_venta, v.total, v.metodo_pago, v.fecha_hora,
                       u.nombre AS cajero,
                       f.id_funcion, f.fecha_hora AS funcion_horario, f.formato,
                       TIME_FORMAT(f.fecha_hora, '%h:%i %p') AS horario_formato,
                       s.nombre_sala,
                       p.titulo AS pelicula, p.clasificacion
                FROM ventas v
                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                INNER JOIN boletos b ON v.id_venta = b.id_venta
                INNER JOIN funciones f ON b.id_funcion = f.id_funcion
                INNER JOIN salas s ON f.id_sala = s.id_sala
                INNER JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                WHERE v.id_venta = :id_venta
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_venta' => $idVenta]);
        $venta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venta) {
            return false;
        }

        // Obtener lista de butacas
        $sqlBoletos = "SELECT fila_columna FROM boletos WHERE id_venta = :id_venta";
        $stmtB = $this->db->prepare($sqlBoletos);
        $stmtB->execute([':id_venta' => $idVenta]);
        $venta['butacas'] = $stmtB->fetchAll(PDO::FETCH_COLUMN) ?: [];

        return $venta;
    }

    public function registrarVentaDulceria(
        int $idUsuario,
        float $total,
        string $metodoPago,
        array $items
    ): array|false {
        if (empty($items) || $total <= 0 || $idUsuario <= 0) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // 1. Insertar en tabla ventas con tipo_venta = 'dulceria'
            $sqlVenta = "INSERT INTO ventas (id_usuario, total, metodo_pago, fecha_hora, tipo_venta)
                         VALUES (:id_usuario, :total, :metodo_pago, NOW(), 'dulceria')";
            $stmtVenta = $this->db->prepare($sqlVenta);
            $stmtVenta->execute([
                ':id_usuario'   => $idUsuario,
                ':total'        => $total,
                ':metodo_pago'  => $metodoPago
            ]);
            $idVenta = (int)$this->db->lastInsertId();

            // 2. Insertar cada item en detalle_ventas_dulceria y descontar stock
            $sqlDetalle = "INSERT INTO detalle_ventas_dulceria (id_venta, id_producto, cantidad, precio_unitario, subtotal, modificadores)
                           VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal, :modificadores)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            $sqlStock = "UPDATE productos 
                         SET stock_disponible = GREATEST(0, stock_disponible - :cantidad) 
                         WHERE id_producto = :id_producto AND stock_disponible > 0";
            $stmtStock = $this->db->prepare($sqlStock);

            foreach ($items as $item) {
                $idProducto = (int)($item['idProducto'] ?? $item['id_producto'] ?? 0);
                $cantidad = max(1, (int)($item['cantidad'] ?? 1));
                $precioUnitario = (float)($item['precio'] ?? $item['precio_unitario'] ?? 0.0);
                $subtotal = $precioUnitario * $cantidad;

                // Modificadores (sabor y extras concatenados para auditoría)
                $modsArray = [];
                if (!empty($item['sabor'])) {
                    $modsArray[] = $item['sabor'];
                }
                if (!empty($item['extras']) && is_array($item['extras'])) {
                    foreach ($item['extras'] as $ex) {
                        $modsArray[] = is_array($ex) ? ($ex['nombre'] ?? '') : (string)$ex;
                    }
                }
                $modificadoresStr = !empty($modsArray) ? implode(', ', array_filter($modsArray)) : null;

                $stmtDetalle->execute([
                    ':id_venta'        => $idVenta,
                    ':id_producto'     => $idProducto,
                    ':cantidad'        => $cantidad,
                    ':precio_unitario' => $precioUnitario,
                    ':subtotal'        => $subtotal,
                    ':modificadores'   => $modificadoresStr
                ]);

                // Descontar inventario disponible
                $stmtStock->execute([
                    ':cantidad'    => $cantidad,
                    ':id_producto' => $idProducto
                ]);
            }

            $this->db->commit();

            return [
                'exito'    => true,
                'id_venta' => $idVenta,
                'total'    => $total,
                'metodo'   => $metodoPago,
                'items'    => $items
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function obtenerVentaDulceriaPorId(int $idVenta): array|false
    {
        $sql = "SELECT v.id_venta, v.total, v.metodo_pago, v.fecha_hora, v.tipo_venta,
                       u.nombre AS cajero
                FROM ventas v
                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.id_venta = :id_venta AND v.tipo_venta = 'dulceria'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_venta' => $idVenta]);
        $venta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venta) {
            return false;
        }

        $sqlItems = "SELECT d.*, p.nombre, p.imagen, p.categoria
                     FROM detalle_ventas_dulceria d
                     INNER JOIN productos p ON d.id_producto = p.id_producto
                     WHERE d.id_venta = :id_venta";
        $stmtItems = $this->db->prepare($sqlItems);
        $stmtItems->execute([':id_venta' => $idVenta]);
        $venta['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return $venta;
    }

    /**
     * Genera un reporte consolidado de ventas filtrado por rango de fechas y/o usuario cajero.
     */
    public function generarReporteVentas(?string $fechaInicio = null, ?string $fechaFin = null, ?int $idUsuario = null): array
    {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($fechaInicio)) {
            $where .= " AND DATE(v.fecha_hora) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $where .= " AND DATE(v.fecha_hora) <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        if (!empty($idUsuario) && $idUsuario > 0) {
            $where .= " AND v.id_usuario = :id_usuario";
            $params[':id_usuario'] = $idUsuario;
        }

        // 1. Métricas Totales
        $sqlTotales = "SELECT 
                          COUNT(v.id_venta) AS total_transacciones,
                          COALESCE(SUM(v.total), 0) AS total_recaudado,
                          COALESCE(SUM(CASE WHEN v.metodo_pago = 'Efectivo' THEN v.total ELSE 0 END), 0) AS total_efectivo,
                          COALESCE(SUM(CASE WHEN v.metodo_pago = 'Tarjeta' THEN v.total ELSE 0 END), 0) AS total_tarjeta,
                          COALESCE(SUM(CASE WHEN v.tipo_venta = 'boleteria' THEN v.total ELSE 0 END), 0) AS total_boletos,
                          COALESCE(SUM(CASE WHEN v.tipo_venta = 'dulceria' THEN v.total ELSE 0 END), 0) AS total_dulceria
                       FROM ventas v
                       $where";
        $stmtTot = $this->db->prepare($sqlTotales);
        $stmtTot->execute($params);
        $totales = $stmtTot->fetch(PDO::FETCH_ASSOC) ?: [];

        // 2. Conteo de unidades (boletos y dulceria)
        $sqlBoletos = "SELECT COUNT(b.id_boleto)
                       FROM boletos b
                       INNER JOIN ventas v ON b.id_venta = v.id_venta
                       $where";
        $stmtBol = $this->db->prepare($sqlBoletos);
        $stmtBol->execute($params);
        $totales['cantidad_boletos'] = (int)$stmtBol->fetchColumn();

        $sqlDulceria = "SELECT COALESCE(SUM(d.cantidad), 0)
                        FROM detalle_ventas_dulceria d
                        INNER JOIN ventas v ON d.id_venta = v.id_venta
                        $where";
        $stmtDul = $this->db->prepare($sqlDulceria);
        $stmtDul->execute($params);
        $totales['cantidad_dulceria'] = (int)$stmtDul->fetchColumn();

        // 3. Listado detallado de transacciones para el cuerpo del reporte
        $sqlTransacciones = "SELECT v.id_venta, v.fecha_hora, v.total, v.metodo_pago, v.tipo_venta,
                                    u.nombre AS nombre_cajero,
                                    p.titulo AS pelicula, s.nombre_sala,
                                    GROUP_CONCAT(DISTINCT b.fila_columna ORDER BY b.fila_columna SEPARATOR ', ') AS butacas,
                                    GROUP_CONCAT(DISTINCT CONCAT(d.cantidad, 'x ', prod.nombre) SEPARATOR ', ') AS productos_dulceria
                             FROM ventas v
                             INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                             LEFT JOIN boletos b ON v.id_venta = b.id_venta
                             LEFT JOIN funciones f ON b.id_funcion = f.id_funcion
                             LEFT JOIN salas s ON f.id_sala = s.id_sala
                             LEFT JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                             LEFT JOIN detalle_ventas_dulceria d ON v.id_venta = d.id_venta
                             LEFT JOIN productos prod ON d.id_producto = prod.id_producto
                             $where
                             GROUP BY v.id_venta
                             ORDER BY v.fecha_hora DESC";
        $stmtTx = $this->db->prepare($sqlTransacciones);
        $stmtTx->execute($params);
        $transacciones = $stmtTx->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return [
            'totales'        => $totales,
            'transacciones'  => $transacciones
        ];
    }

    /**
     * Genera un reporte de ranking de ventas de dulcería por producto.
     */
    public function generarReporteRankingDulceria(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($fechaInicio)) {
            $where .= " AND DATE(v.fecha_hora) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $where .= " AND DATE(v.fecha_hora) <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        $sql = "SELECT p.id_producto, p.nombre, p.categoria, p.precio, p.stock_disponible, p.imagen,
                       COALESCE(SUM(d.cantidad), 0) AS unidades_vendidas,
                       COALESCE(SUM(d.subtotal), 0) AS total_recaudado
                FROM productos p
                LEFT JOIN detalle_ventas_dulceria d ON p.id_producto = d.id_producto
                LEFT JOIN ventas v ON d.id_venta = v.id_venta " . (!empty($params) ? $where : "") . "
                GROUP BY p.id_producto
                ORDER BY total_recaudado DESC, unidades_vendidas DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
