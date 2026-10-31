<?php

require_once __DIR__ . '/../config/Database.php';

class Caja
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function obtenerTurnoAbierto(int $idUsuario): array|false
    {
        $sql = "SELECT id_corte, id_usuario, fecha_inicio, monto_inicial
                FROM cortes_caja
                WHERE id_usuario = :id_usuario AND fecha_cierre IS NULL
                ORDER BY id_corte DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function abrirTurno(int $idUsuario, float $montoInicial): int|false
    {
        // Verificar si ya existe un turno abierto
        $turnoExistente = $this->obtenerTurnoAbierto($idUsuario);
        if ($turnoExistente) {
            return (int)$turnoExistente['id_corte'];
        }

        $sql = "INSERT INTO cortes_caja (id_usuario, fecha_inicio, monto_inicial, total_ventas, total_declarado, diferencia)
                VALUES (:id_usuario, NOW(), :monto_inicial, 0.00, 0.00, 0.00)";

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':id_usuario'    => $idUsuario,
            ':monto_inicial' => $montoInicial
        ]);

        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    public function obtenerResumenTurno(int $idUsuario, string $fechaInicio): array
    {
        // 1. Total ventas general del turno con desglose por categoría y método de pago
        $sqlTotales = "SELECT 
                          COUNT(id_venta) AS cantidad_ventas,
                          COALESCE(SUM(total), 0) AS total_ventas,
                          COALESCE(SUM(CASE WHEN metodo_pago = 'Efectivo' THEN total ELSE 0 END), 0) AS total_efectivo,
                          COALESCE(SUM(CASE WHEN metodo_pago = 'Tarjeta' THEN total ELSE 0 END), 0) AS total_tarjeta,
                          COALESCE(SUM(CASE WHEN tipo_venta = 'boleteria' THEN total ELSE 0 END), 0) AS total_boletos,
                          COALESCE(SUM(CASE WHEN tipo_venta = 'dulceria' THEN total ELSE 0 END), 0) AS total_dulceria
                       FROM ventas
                       WHERE id_usuario = :id_usuario AND fecha_hora >= :fecha_inicio";

        $stmt = $this->db->prepare($sqlTotales);
        $stmt->execute([
            ':id_usuario'    => $idUsuario,
            ':fecha_inicio'  => $fechaInicio
        ]);
        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Cantidad de boletos vendidos
        $sqlBoletos = "SELECT COUNT(b.id_boleto) AS cantidad_boletos
                       FROM boletos b
                       INNER JOIN ventas v ON b.id_venta = v.id_venta
                       WHERE v.id_usuario = :id_usuario AND v.fecha_hora >= :fecha_inicio";
        $stmtB = $this->db->prepare($sqlBoletos);
        $stmtB->execute([
            ':id_usuario'   => $idUsuario,
            ':fecha_inicio' => $fechaInicio
        ]);
        $boletosRow = $stmtB->fetch(PDO::FETCH_ASSOC);
        $resumen['cantidad_boletos'] = (int)($boletosRow['cantidad_boletos'] ?? 0);

        // 3. Cantidad de artículos de dulcería vendidos
        $sqlDulceria = "SELECT COALESCE(SUM(d.cantidad), 0) AS cantidad_dulceria
                        FROM detalle_ventas_dulceria d
                        INNER JOIN ventas v ON d.id_venta = v.id_venta
                        WHERE v.id_usuario = :id_usuario AND v.fecha_hora >= :fecha_inicio";
        $stmtD = $this->db->prepare($sqlDulceria);
        $stmtD->execute([
            ':id_usuario'   => $idUsuario,
            ':fecha_inicio' => $fechaInicio
        ]);
        $dulceriaRow = $stmtD->fetch(PDO::FETCH_ASSOC);
        $resumen['cantidad_dulceria'] = (int)($dulceriaRow['cantidad_dulceria'] ?? 0);

        // 4. Detalle de transacciones del turno para auditoría (soporta tanto boletería como dulcería)
        $sqlTransacciones = "SELECT v.id_venta, v.fecha_hora, v.total, v.metodo_pago, v.tipo_venta,
                                    p.titulo AS pelicula, s.nombre_sala,
                                    GROUP_CONCAT(DISTINCT b.fila_columna ORDER BY b.fila_columna SEPARATOR ', ') AS butacas,
                                    GROUP_CONCAT(DISTINCT CONCAT(d.cantidad, 'x ', prod.nombre) SEPARATOR ', ') AS productos_dulceria
                             FROM ventas v
                             LEFT JOIN boletos b ON v.id_venta = b.id_venta
                             LEFT JOIN funciones f ON b.id_funcion = f.id_funcion
                             LEFT JOIN salas s ON f.id_sala = s.id_sala
                             LEFT JOIN peliculas p ON f.id_pelicula = p.id_pelicula
                             LEFT JOIN detalle_ventas_dulceria d ON v.id_venta = d.id_venta
                             LEFT JOIN productos prod ON d.id_producto = prod.id_producto
                             WHERE v.id_usuario = :id_usuario AND v.fecha_hora >= :fecha_inicio
                             GROUP BY v.id_venta
                             ORDER BY v.fecha_hora DESC";
        $stmtT = $this->db->prepare($sqlTransacciones);
        $stmtT->execute([
            ':id_usuario'   => $idUsuario,
            ':fecha_inicio' => $fechaInicio
        ]);
        $resumen['transacciones'] = $stmtT->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return $resumen;
    }

    public function cerrarTurno(
        int $idCorte,
        float $totalVentas,
        float $totalDeclarado,
        float $diferencia
    ): bool {
        $sql = "UPDATE cortes_caja
                SET fecha_cierre = NOW(),
                    total_ventas = :total_ventas,
                    total_declarado = :total_declarado,
                    diferencia = :diferencia
                WHERE id_corte = :id_corte AND fecha_cierre IS NULL";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_corte'         => $idCorte,
            ':total_ventas'     => $totalVentas,
            ':total_declarado'  => $totalDeclarado,
            ':diferencia'       => $diferencia
        ]);
    }

    public function obtenerCortePorId(int $idCorte): array|false
    {
        $sql = "SELECT c.*, u.nombre AS cajero
                FROM cortes_caja c
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE c.id_corte = :id_corte
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_corte' => $idCorte]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    // Lista todos los cortes de caja con filtros opcionales de fecha y usuario
    public function listarTodosLosCortes(?string $fecha = null, ?int $idUsuario = null): array
    {
        $sql = "SELECT c.*, u.nombre AS nombre_cajero, u.usuario AS usuario_cajero,
                       DATE_FORMAT(c.fecha_inicio, '%d/%m/%Y %h:%i %p') AS inicio_formato,
                       DATE_FORMAT(c.fecha_cierre, '%d/%m/%Y %h:%i %p') AS cierre_formato
                FROM cortes_caja c
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE 1=1";

        $params = [];

        if (!empty($fecha)) {
            $sql .= " AND DATE(c.fecha_inicio) = :fecha";
            $params[':fecha'] = $fecha;
        }

        if (!empty($idUsuario) && $idUsuario > 0) {
            $sql .= " AND c.id_usuario = :id_usuario";
            $params[':id_usuario'] = $idUsuario;
        }

        $sql .= " ORDER BY c.fecha_inicio DESC, c.id_corte DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Obtiene informacion detallada y transacciones de un corte para reporte o reimpresion
    public function obtenerDetalleCompletoCorte(int $idCorte): array|false
    {
        $corte = $this->obtenerCortePorId($idCorte);
        if (!$corte) {
            return false;
        }

        $fechaInicio = $corte['fecha_inicio'];
        $fechaCierre = $corte['fecha_cierre'] ?? date('Y-m-d H:i:s');
        $idUsuario   = (int)$corte['id_usuario'];

        $sqlVentas = "SELECT 
                          COUNT(id_venta) AS cantidad_ventas,
                          COALESCE(SUM(total), 0) AS total_ventas,
                          COALESCE(SUM(CASE WHEN metodo_pago = 'Efectivo' THEN total ELSE 0 END), 0) AS total_efectivo,
                          COALESCE(SUM(CASE WHEN metodo_pago = 'Tarjeta' THEN total ELSE 0 END), 0) AS total_tarjeta,
                          COALESCE(SUM(CASE WHEN tipo_venta = 'boleteria' THEN total ELSE 0 END), 0) AS total_boletos,
                          COALESCE(SUM(CASE WHEN tipo_venta = 'dulceria' THEN total ELSE 0 END), 0) AS total_dulceria
                      FROM ventas
                      WHERE id_usuario = :id_usuario 
                        AND fecha_hora >= :fecha_inicio 
                        AND fecha_hora <= :fecha_cierre";

        $stmtV = $this->db->prepare($sqlVentas);
        $stmtV->execute([
            ':id_usuario'   => $idUsuario,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_cierre' => $fechaCierre
        ]);
        $totales = $stmtV->fetch(PDO::FETCH_ASSOC) ?: [];

        $sqlBoletos = "SELECT COUNT(b.id_boleto)
                       FROM boletos b
                       INNER JOIN ventas v ON b.id_venta = v.id_venta
                       WHERE v.id_usuario = :id_usuario 
                         AND v.fecha_hora >= :fecha_inicio 
                         AND v.fecha_hora <= :fecha_cierre";
        $stmtB = $this->db->prepare($sqlBoletos);
        $stmtB->execute([
            ':id_usuario'   => $idUsuario,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_cierre' => $fechaCierre
        ]);
        $cantidadBoletos = (int)$stmtB->fetchColumn();

        $sqlDulceria = "SELECT COALESCE(SUM(d.cantidad), 0)
                        FROM detalle_ventas_dulceria d
                        INNER JOIN ventas v ON d.id_venta = v.id_venta
                        WHERE v.id_usuario = :id_usuario 
                          AND v.fecha_hora >= :fecha_inicio 
                          AND v.fecha_hora <= :fecha_cierre";
        $stmtD = $this->db->prepare($sqlDulceria);
        $stmtD->execute([
            ':id_usuario'   => $idUsuario,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_cierre' => $fechaCierre
        ]);
        $cantidadDulceria = (int)$stmtD->fetchColumn();

        $corte['total_efectivo']    = (float)($totales['total_efectivo'] ?? 0);
        $corte['total_tarjeta']     = (float)($totales['total_tarjeta'] ?? 0);
        $corte['total_boletos']     = (float)($totales['total_boletos'] ?? 0);
        $corte['total_dulceria']    = (float)($totales['total_dulceria'] ?? 0);
        $corte['cantidad_ventas']   = (int)($totales['cantidad_ventas'] ?? 0);
        $corte['cantidad_boletos']  = $cantidadBoletos;
        $corte['cantidad_dulceria'] = $cantidadDulceria;
        $corte['esperado_gaveta']   = (float)$corte['monto_inicial'] + (float)$corte['total_efectivo'];

        return $corte;
    }

    /**
     * Genera un reporte consolidado de cortes y turnos de caja para auditoría impresa.
     */
    public function generarReporteConsolidadoCortes(?string $fechaInicio = null, ?string $fechaFin = null, ?int $idUsuario = null): array
    {
        $sql = "SELECT c.*, u.nombre AS nombre_cajero, u.usuario AS usuario_cajero,
                       DATE_FORMAT(c.fecha_inicio, '%d/%m/%Y %h:%i %p') AS inicio_formato,
                       DATE_FORMAT(c.fecha_cierre, '%d/%m/%Y %h:%i %p') AS cierre_formato
                FROM cortes_caja c
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                WHERE 1=1";

        $params = [];

        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(c.fecha_inicio) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $sql .= " AND DATE(c.fecha_inicio) <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        if (!empty($idUsuario) && $idUsuario > 0) {
            $sql .= " AND c.id_usuario = :id_usuario";
            $params[':id_usuario'] = $idUsuario;
        }

        $sql .= " ORDER BY c.fecha_inicio DESC, c.id_corte DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $cortes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $totalCortes = count($cortes);
        $totalCerrados = 0;
        $totalVentas = 0.0;
        $totalFaltantes = 0.0;
        $totalSobrantes = 0.0;
        $totalDeclarado = 0.0;
        $totalFondoInicial = 0.0;

        foreach ($cortes as $c) {
            $totalFondoInicial += (float)$c['monto_inicial'];
            if (!empty($c['fecha_cierre'])) {
                $totalCerrados++;
                $totalVentas += (float)$c['total_ventas'];
                $totalDeclarado += (float)$c['total_declarado'];
                $dif = (float)$c['diferencia'];
                if ($dif < 0) {
                    $totalFaltantes += abs($dif);
                } elseif ($dif > 0) {
                    $totalSobrantes += $dif;
                }
            }
        }

        return [
            'cortes'              => $cortes,
            'total_cortes'        => $totalCortes,
            'total_cerrados'      => $totalCerrados,
            'total_ventas'        => $totalVentas,
            'total_declarado'     => $totalDeclarado,
            'total_fondo_inicial' => $totalFondoInicial,
            'total_faltantes'     => $totalFaltantes,
            'total_sobrantes'     => $totalSobrantes
        ];
    }
}
