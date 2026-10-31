<?php

require_once __DIR__ . '/../models/Caja.php';

class CajaController
{
    private function verificarAutenticacion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public function apertura(): void
    {
        $this->verificarAutenticacion();

        $modeloCaja = new Caja();
        $turnoAbierto = $modeloCaja->obtenerTurnoAbierto((int)$_SESSION['id_usuario']);

        if ($turnoAbierto) {
            header('Location: index.php?action=boleteria');
            exit;
        }

        $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
        $nombreRol    = $_SESSION['nombre_rol'] ?? 'Operativo';
        $mensajeAviso = $_SESSION['aviso_caja'] ?? null;
        unset($_SESSION['aviso_caja']);

        require_once __DIR__ . '/../views/caja/apertura.php';
    }

    public function procesarApertura(): void
    {
        $this->verificarAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=apertura-caja');
            exit;
        }

        $montoInicial = (float)($_POST['monto_inicial'] ?? 0);
        if ($montoInicial < 0) {
            $montoInicial = 0;
        }

        $modeloCaja = new Caja();
        $idCorte = $modeloCaja->abrirTurno((int)$_SESSION['id_usuario'], $montoInicial);

        if ($idCorte) {
            $_SESSION['id_corte_activo'] = $idCorte;
        }

        header('Location: index.php?action=boleteria');
        exit;
    }

    public function cierre(): void
    {
        $this->verificarAutenticacion();

        $modeloCaja = new Caja();
        $turnoAbierto = $modeloCaja->obtenerTurnoAbierto((int)$_SESSION['id_usuario']);

        $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
        $nombreRol    = $_SESSION['nombre_rol'] ?? 'Operativo';

        if (!$turnoAbierto) {
            require_once __DIR__ . '/../views/caja/sin_turno.php';
            return;
        }

        $resumen = $modeloCaja->obtenerResumenTurno((int)$_SESSION['id_usuario'], $turnoAbierto['fecha_inicio']);

        $montoInicial      = (float)$turnoAbierto['monto_inicial'];
        $totalVentas       = (float)$resumen['total_ventas'];
        $ventasEfectivo    = (float)$resumen['total_efectivo'];
        $ventasTarjeta     = (float)$resumen['total_tarjeta'];
        $totalBoletos      = (float)($resumen['total_boletos'] ?? 0);
        $totalDulceria     = (float)($resumen['total_dulceria'] ?? 0);
        $cantidadBoletos   = (int)($resumen['cantidad_boletos'] ?? 0);
        $cantidadDulceria  = (int)($resumen['cantidad_dulceria'] ?? 0);
        $esperadoEnGaveta  = $montoInicial + $ventasEfectivo;
        $transacciones     = $resumen['transacciones'];

        require_once __DIR__ . '/../views/caja/cierre.php';
    }

    public function procesarCierre(): void
    {
        $this->verificarAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=cierre-caja');
            exit;
        }

        $idCorte        = (int)($_POST['id_corte'] ?? 0);
        $totalVentas    = (float)($_POST['total_ventas'] ?? 0);
        $totalDeclarado = (float)($_POST['total_declarado'] ?? 0);
        $diferencia     = (float)($_POST['diferencia'] ?? 0);

        if ($idCorte <= 0) {
            header('Location: index.php?action=boleteria');
            exit;
        }

        $modeloCaja = new Caja();
        $ok = $modeloCaja->cerrarTurno($idCorte, $totalVentas, $totalDeclarado, $diferencia);

        unset($_SESSION['id_corte_activo']);

        if ($ok) {
            $corteInfo = $modeloCaja->obtenerDetalleCompletoCorte($idCorte);
            if (!$corteInfo) {
                $corteInfo = $modeloCaja->obtenerCortePorId($idCorte);
            }
            $nombreCajero = $_SESSION['nombre'] ?? 'Cajero';
            require_once __DIR__ . '/../views/caja/resumen_corte.php';
        } else {
            header('Location: index.php?action=cierre-caja');
        }
    }
}
