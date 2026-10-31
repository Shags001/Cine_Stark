<?php

require_once __DIR__ . '/../config/Database.php';

class Producto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lista productos con filtros opcionales de categoría, búsqueda y estado.
     */
    public function listarTodos(?string $categoria = null, ?string $busqueda = null, bool $soloActivos = false): array
    {
        $sql = "SELECT p.*,
                       (SELECT COALESCE(SUM(d.cantidad), 0) FROM detalle_ventas_dulceria d WHERE d.id_producto = p.id_producto) AS total_vendidos
                FROM productos p
                WHERE 1=1";

        $params = [];

        if (!empty($categoria) && $categoria !== 'todos') {
            $sql .= " AND p.categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        if (!empty($busqueda)) {
            $sql .= " AND (p.nombre LIKE :busqueda OR p.descripcion LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        if ($soloActivos) {
            $sql .= " AND p.activo = 1";
        }

        $sql .= " ORDER BY p.categoria ASC, p.id_producto ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene un producto por su clave primaria.
     */
    public function obtenerPorId(int $idProducto): array|false
    {
        $sql = "SELECT * FROM productos WHERE id_producto = :id_producto LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_producto' => $idProducto]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    /**
     * Registra un nuevo producto en el catálogo.
     */
    public function crear(
        string $nombre,
        string $categoria,
        ?string $descripcion,
        float $precio,
        ?string $imagen,
        int $personalizable,
        int $stockDisponible,
        int $activo = 1
    ): int|false {
        $sql = "INSERT INTO productos (nombre, categoria, descripcion, precio, imagen, personalizable, stock_disponible, activo)
                VALUES (:nombre, :categoria, :descripcion, :precio, :imagen, :personalizable, :stock_disponible, :activo)";

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':nombre'           => trim($nombre),
            ':categoria'        => trim($categoria),
            ':descripcion'      => !empty($descripcion) ? trim($descripcion) : null,
            ':precio'           => $precio,
            ':imagen'           => !empty($imagen) ? trim($imagen) : null,
            ':personalizable'   => $personalizable ? 1 : 0,
            ':stock_disponible' => max(0, $stockDisponible),
            ':activo'           => $activo ? 1 : 0
        ]);

        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Actualiza los datos de un producto existente.
     */
    public function actualizar(
        int $idProducto,
        string $nombre,
        string $categoria,
        ?string $descripcion,
        float $precio,
        ?string $imagen,
        int $personalizable,
        int $stockDisponible,
        int $activo
    ): bool {
        $sql = "UPDATE productos
                SET nombre = :nombre,
                    categoria = :categoria,
                    descripcion = :descripcion,
                    precio = :precio,
                    imagen = :imagen,
                    personalizable = :personalizable,
                    stock_disponible = :stock_disponible,
                    activo = :activo
                WHERE id_producto = :id_producto";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_producto'      => $idProducto,
            ':nombre'           => trim($nombre),
            ':categoria'        => trim($categoria),
            ':descripcion'      => !empty($descripcion) ? trim($descripcion) : null,
            ':precio'           => $precio,
            ':imagen'           => !empty($imagen) ? trim($imagen) : null,
            ':personalizable'   => $personalizable ? 1 : 0,
            ':stock_disponible' => max(0, $stockDisponible),
            ':activo'           => $activo ? 1 : 0
        ]);
    }

    /**
     * Cambia el estado activo/inactivo de un producto.
     */
    public function toggleEstado(int $idProducto): bool
    {
        $sql = "UPDATE productos SET activo = IF(activo = 1, 0, 1) WHERE id_producto = :id_producto";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_producto' => $idProducto]);
    }

    /**
     * Elimina un producto de forma segura:
     * Si tiene ventas registradas en detalle_ventas_dulceria, se desactiva (soft delete) para proteger integridad referencial.
     * Si no tiene ventas, se elimina físicamente de la tabla.
     */
    public function eliminar(int $idProducto): array
    {
        // Verificar si tiene ventas registradas
        $sqlCheck = "SELECT COUNT(*) FROM detalle_ventas_dulceria WHERE id_producto = :id_producto";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':id_producto' => $idProducto]);
        $ventasCount = (int)$stmtCheck->fetchColumn();

        if ($ventasCount > 0) {
            // Desactivar para preservar historial contable
            $sqlDesactivar = "UPDATE productos SET activo = 0 WHERE id_producto = :id_producto";
            $stmtDesact = $this->db->prepare($sqlDesactivar);
            $stmtDesact->execute([':id_producto' => $idProducto]);

            return [
                'accion' => 'desactivado',
                'mensaje' => "El producto tiene $ventasCount venta(s) histórica(s) registrada(s), por lo que fue desactivado del catálogo para mantener la integridad contable."
            ];
        }

        // Si no tiene ventas, eliminar físicamente
        $sqlDelete = "DELETE FROM productos WHERE id_producto = :id_producto";
        $stmtDelete = $this->db->prepare($sqlDelete);
        $stmtDelete->execute([':id_producto' => $idProducto]);

        return [
            'accion' => 'eliminado',
            'mensaje' => 'El producto fue eliminado correctamente del sistema.'
        ];
    }

    /**
     * Cuenta el total de productos en catálogo.
     */
    public function contarTotal(): int
    {
        $sql = "SELECT COUNT(*) FROM productos";
        return (int)$this->db->query($sql)->fetchColumn();
    }

    /**
     * Retorna las categorías disponibles en el sistema con sus cantidades.
     */
    public function obtenerCategorias(): array
    {
        $sql = "SELECT categoria, COUNT(*) AS cantidad 
                FROM productos 
                GROUP BY categoria 
                ORDER BY categoria ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Genera reporte de inventario valorizado y existencias de dulcería.
     */
    public function generarReporteInventario(): array
    {
        $sql = "SELECT p.*,
                       (p.stock_disponible * p.precio) AS valor_stock,
                       (SELECT COALESCE(SUM(d.cantidad), 0) FROM detalle_ventas_dulceria d WHERE d.id_producto = p.id_producto) AS total_vendidos
                FROM productos p
                ORDER BY p.categoria ASC, p.stock_disponible ASC, p.nombre ASC";

        $productos = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $totalArticulos = count($productos);
        $totalStockUnidades = 0;
        $totalValorInventario = 0.0;
        $totalAgotados = 0;
        $totalBajoStock = 0;

        foreach ($productos as $p) {
            $stock = (int)$p['stock_disponible'];
            $totalStockUnidades += $stock;
            $totalValorInventario += (float)$p['valor_stock'];
            if ($stock <= 0) {
                $totalAgotados++;
            } elseif ($stock <= 10) {
                $totalBajoStock++;
            }
        }

        return [
            'productos'              => $productos,
            'total_articulos'        => $totalArticulos,
            'total_stock_unidades'   => $totalStockUnidades,
            'total_valor_inventario' => $totalValorInventario,
            'total_agotados'         => $totalAgotados,
            'total_bajo_stock'       => $totalBajoStock
        ];
    }
}

