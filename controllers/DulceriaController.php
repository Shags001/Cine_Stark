<?php

require_once __DIR__ . '/../models/Caja.php';

class DulceriaController
{
    private function verificarAutenticacion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public function index(): void
    {
        $this->verificarAutenticacion();

        $modeloCaja = new Caja();
        $turnoActivo = $modeloCaja->obtenerTurnoAbierto((int)$_SESSION['id_usuario']);

        // Regla estricta CineStark: No se permite operar en el POS de dulcería sin tener turno de caja abierto
        if (!$turnoActivo) {
            $_SESSION['aviso_caja'] = 'Debe realizar la apertura de caja para registrar su turno antes de poder operar en dulcería.';
            header('Location: index.php?action=apertura-caja');
            exit;
        }

        $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
        $nombreRolStr = $_SESSION['nombre_rol'] ?? 'Cajero';

        // Cargar productos activos desde la base de datos
        require_once __DIR__ . '/../models/Producto.php';
        $modeloProducto = new Producto();
        $productosBD = $modeloProducto->listarTodos(soloActivos: true);

        $iconosCategorias = [
            'combos'    => 'bi-stars',
            'palomitas' => 'bi-box2',
            'bebidas'   => 'bi-cup-straw',
            'snacks'    => 'bi-fire',
            'dulces'    => 'bi-bag-heart',
        ];

        $catalogoPos = [];
        foreach ($productosBD as $p) {
            $cat = strtolower(trim($p['categoria']));
            $esPersonalizable = ((int)$p['personalizable'] === 1);
            $sabores = [];
            $extras = [];

            if ($esPersonalizable) {
                if ($cat === 'palomitas') {
                    $sabores = [
                        ['nombre' => 'Mantequilla Tradicional', 'extra' => 0.00, 'default' => true],
                        ['nombre' => 'Saladas Clásicas', 'extra' => 0.00],
                        ['nombre' => 'Caramelo Dulce', 'extra' => 0.75],
                        ['nombre' => 'Mixtas (Sal + Caramelo)', 'extra' => 0.75],
                    ];
                    $extras = [
                        ['nombre' => 'Extra Mantequilla Líquida', 'precio' => 0.75],
                        ['nombre' => 'Queso Cheddar Derretido', 'precio' => 1.00],
                        ['nombre' => 'Sazonador Queso / Jalapeño', 'precio' => 0.50],
                    ];
                } elseif ($cat === 'combos') {
                    $sabores = [
                        ['nombre' => 'Popcorn Mantequilla', 'extra' => 0.00, 'default' => true],
                        ['nombre' => 'Popcorn Saladas', 'extra' => 0.00],
                        ['nombre' => 'Popcorn Caramelo', 'extra' => 0.75],
                        ['nombre' => 'Popcorn Mixtas', 'extra' => 0.75],
                    ];
                    $extras = [
                        ['nombre' => 'Extra Mantequilla Líquida', 'precio' => 0.75],
                        ['nombre' => 'Agrandar Bebidas a Grandes', 'precio' => 1.00],
                        ['nombre' => 'Doble Queso / Aderezo', 'precio' => 1.00],
                    ];
                } elseif ($cat === 'bebidas') {
                    $sabores = [
                        ['nombre' => 'Coca-Cola Original', 'extra' => 0.00, 'default' => true],
                        ['nombre' => 'Coca-Cola Zero', 'extra' => 0.00],
                        ['nombre' => 'Sprite', 'extra' => 0.00],
                        ['nombre' => 'Fanta Naranja', 'extra' => 0.00],
                    ];
                } elseif ($cat === 'snacks') {
                    $sabores = [
                        ['nombre' => 'Con Todo (Aderezos Tradicionales)', 'extra' => 0.00, 'default' => true],
                        ['nombre' => 'Solo Salsas Básicas', 'extra' => 0.00],
                        ['nombre' => 'Sin Salsas / Natural', 'extra' => 0.00],
                    ];
                    $extras = [
                        ['nombre' => 'Doble Queso Cheddar', 'precio' => 1.00],
                        ['nombre' => 'Jalapeños Extra', 'precio' => 0.50],
                        ['nombre' => 'Tocino Crocante', 'precio' => 1.00],
                    ];
                }
            }

            $catalogoPos[] = [
                'id'               => (int)$p['id_producto'],
                'nombre'           => $p['nombre'],
                'categoria'        => $cat,
                'imagen'           => !empty($p['imagen']) ? $p['imagen'] : 'img/cinestarklogo.png',
                'iconoPlaceholder' => $iconosCategorias[$cat] ?? 'bi-box-seam',
                'precio'           => (float)$p['precio'],
                'descripcion'      => $p['descripcion'] ?? '',
                'personalizable'   => $esPersonalizable,
                'stock'            => (int)$p['stock_disponible'],
                'sabores'          => $sabores,
                'extras'           => $extras,
            ];
        }

        require_once __DIR__ . '/../views/dulceria/pos.php';
    }

    public function procesarVenta(): void
    {
        $this->verificarAutenticacion();

        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['exito' => false, 'error' => 'Método no permitido. Se requiere POST.']);
            exit;
        }

        $idUsuario = (int)($_SESSION['id_usuario'] ?? 0);

        $modeloCaja = new Caja();
        $turnoActivo = $modeloCaja->obtenerTurnoAbierto($idUsuario);

        // Bloqueo estricto a nivel de backend: Imposible registrar ventas sin turno de caja activo
        if (!$turnoActivo) {
            echo json_encode([
                'exito' => false,
                'error' => 'No tiene un turno de caja abierto. Debe realizar la apertura de turno con su fondo de caja para poder vender.'
            ]);
            exit;
        }

        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!$data || empty($data['items']) || !is_array($data['items'])) {
            echo json_encode(['exito' => false, 'error' => 'No se han enviado artículos para registrar la venta.']);
            exit;
        }

        $total = (float)($data['total'] ?? 0);
        if ($total <= 0) {
            echo json_encode(['exito' => false, 'error' => 'El monto total a cobrar debe ser superior a cero.']);
            exit;
        }

        $metodoPagoInput = strtolower(trim($data['metodo_pago'] ?? 'efectivo'));
        $metodoPago = ($metodoPagoInput === 'tarjeta') ? 'Tarjeta' : 'Efectivo';

        require_once __DIR__ . '/../models/Venta.php';
        $modeloVenta = new Venta();
        $resultado = $modeloVenta->registrarVentaDulceria($idUsuario, $total, $metodoPago, $data['items']);

        if (!$resultado || empty($resultado['exito'])) {
            echo json_encode(['exito' => false, 'error' => 'Ocurrió un error al persistir la transacción en base de datos.']);
            exit;
        }

        $idVenta = (int)$resultado['id_venta'];
        $folioTicket = '#D-' . str_pad($idVenta, 5, '0', STR_PAD_LEFT);
        $codigoCanje = '#CANJE-' . (2000 + ($idVenta % 7999));

        echo json_encode([
            'exito'         => true,
            'id_venta'      => $idVenta,
            'folio_ticket'  => $folioTicket,
            'codigo_canje'  => $codigoCanje,
            'total'         => $total,
            'metodo_pago'   => $metodoPago,
            'fecha_hora'    => date('d/m/Y h:i A')
        ]);
        exit;
    }
}

