<?php

require_once __DIR__ . '/../models/Funcion.php';
require_once __DIR__ . '/../models/Boleto.php';
require_once __DIR__ . '/../models/Caja.php';

class BoleteriaController
{
    private function verificarAutenticacion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    // Valida que el cajero tenga un turno de caja abierto antes de operar
    private function verificarTurnoAbierto(): array
    {
        $this->verificarAutenticacion();

        $modeloCaja = new Caja();
        $turnoAbierto = $modeloCaja->obtenerTurnoAbierto((int)$_SESSION['id_usuario']);

        if (!$turnoAbierto) {
            $_SESSION['aviso_caja'] = 'Debe realizar la apertura de caja para registrar su turno antes de poder vender boletos.';
            header('Location: index.php?action=apertura-caja');
            exit;
        }

        return $turnoAbierto;
    }

    public function paso1(): void
    {
        $this->verificarAutenticacion();

        $modeloCaja = new Caja();
        $turnoActivo = $modeloCaja->obtenerTurnoAbierto((int)$_SESSION['id_usuario']);

        // Regla estricta CineStark: No se permite operar ni vender en boletería sin turno de caja abierto
        if (!$turnoActivo) {
            $_SESSION['aviso_caja'] = 'Debe realizar la apertura de caja para registrar su turno antes de poder operar en boletería.';
            header('Location: index.php?action=apertura-caja');
            exit;
        }

        $fechaSeleccionada = trim($_GET['fecha'] ?? date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
            $fechaSeleccionada = date('Y-m-d');
        }

        $modeloFuncion = new Funcion();
        $salasConFunciones = $modeloFuncion->obtenerFuncionesDelDia($fechaSeleccionada);

        $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
        $nombreRol    = $_SESSION['nombre_rol'] ?? 'Operativo';

        require_once __DIR__ . '/../views/boleteria/funciones.php';
    }

    public function paso2(): void
    {
        $turnoActivo = $this->verificarTurnoAbierto();

        $idFuncion = (int)($_GET['id_funcion'] ?? 0);
        if ($idFuncion <= 0) {
            header('Location: index.php?action=boleteria');
            exit;
        }

        $modeloBoleto = new Boleto();
        $detalleFuncion = $modeloBoleto->obtenerDetalleFuncion($idFuncion);

        if (!$detalleFuncion) {
            header('Location: index.php?action=boleteria');
            exit;
        }

        $butacasOcupadas = $modeloBoleto->obtenerButacasOcupadas($idFuncion);

        // 10 filas (A a J) y 11 columnas (1 a 11) = 110 butacas
        $filas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $columnas = range(1, 11);

        $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
        $nombreRol    = $_SESSION['nombre_rol'] ?? 'Operativo';

        require_once __DIR__ . '/../views/boleteria/butacas.php';
    }

    public function paso3(): void
    {
        $turnoActivo = $this->verificarTurnoAbierto();

        $idFuncion = (int)($_POST['id_funcion'] ?? $_GET['id_funcion'] ?? 0);
        $strButacas = trim($_POST['butacas'] ?? $_GET['butacas'] ?? '');

        if ($idFuncion <= 0 || $strButacas === '') {
            header('Location: index.php?action=boleteria');
            exit;
        }

        $butacas = array_filter(array_map('trim', explode(',', $strButacas)));
        if (empty($butacas)) {
            header('Location: index.php?action=paso2&id_funcion=' . $idFuncion);
            exit;
        }

        $modeloBoleto = new Boleto();
        $detalleFuncion = $modeloBoleto->obtenerDetalleFuncion($idFuncion);

        if (!$detalleFuncion) {
            header('Location: index.php?action=boleteria');
            exit;
        }

        $precioGeneral = (float)$detalleFuncion['precio_boleto'];
        $precioNinos   = round($precioGeneral * 0.75, 2);
        $precioPromo   = round($precioGeneral * 0.65, 2);

        $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
        $nombreRol    = $_SESSION['nombre_rol'] ?? 'Operativo';

        require_once __DIR__ . '/../views/boleteria/cobro.php';
    }

    public function procesarVenta(): void
    {
        $turnoActivo = $this->verificarTurnoAbierto();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=boleteria');
            exit;
        }

        $idFuncion   = (int)($_POST['id_funcion'] ?? 0);
        $strButacas  = trim($_POST['butacas'] ?? '');
        $tarifa      = trim($_POST['tarifa'] ?? 'General');
        $metodoPago  = trim($_POST['metodo_pago'] ?? 'Efectivo');
        $total       = (float)($_POST['total'] ?? 0);
        $efectivoRec = (float)($_POST['efectivo_recibido'] ?? $total);
        $cambio      = (float)($_POST['cambio'] ?? 0);

        $butacas = array_filter(array_map('trim', explode(',', $strButacas)));

        if ($idFuncion <= 0 || empty($butacas) || $total <= 0) {
            header('Location: index.php?action=boleteria');
            exit;
        }

        require_once __DIR__ . '/../models/Venta.php';
        $modeloVenta = new Venta();
        $resultado = $modeloVenta->registrarVentaBoleteria(
            (int)$_SESSION['id_usuario'],
            $idFuncion,
            $butacas,
            $total,
            $metodoPago
        );

        if ($resultado === false) {
            $_SESSION['error_venta'] = 'Error al procesar la venta. Intente de nuevo.';
            header('Location: index.php?action=paso2&id_funcion=' . $idFuncion);
            exit;
        }

        if (isset($resultado['error']) && $resultado['error'] === 'conflicto') {
            $_SESSION['error_venta'] = 'Las siguientes butacas acaban de ser ocupadas: ' . implode(', ', $resultado['butacas_conflicto']);
            header('Location: index.php?action=paso2&id_funcion=' . $idFuncion);
            exit;
        }

        // Obtener datos completos para el ticket termico
        $ventaInfo = $modeloVenta->obtenerVentaPorId($resultado['id_venta']);
        $ventaInfo['tarifa'] = $tarifa;
        $ventaInfo['efectivo_recibido'] = $efectivoRec;
        $ventaInfo['cambio'] = $cambio;

        $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
        $nombreRol    = $_SESSION['nombre_rol'] ?? 'Operativo';

        require_once __DIR__ . '/../views/boleteria/ticket.php';
    }
}
