<?php

// Zona horaria local
date_default_timezone_set('America/El_Salvador');

session_start();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/BoleteriaController.php';
require_once __DIR__ . '/controllers/CajaController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/DulceriaController.php';

$action = $_GET['action'] ?? 'login';
$authController = new AuthController();

switch ($action) {
    case 'procesar-login':
        $authController->login();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'boleteria':
    case 'paso1':
        $boleteriaController = new BoleteriaController();
        $boleteriaController->paso1();
        break;

    case 'paso2':
        $boleteriaController = new BoleteriaController();
        $boleteriaController->paso2();
        break;

    case 'paso3':
        $boleteriaController = new BoleteriaController();
        $boleteriaController->paso3();
        break;

    case 'procesar-venta':
        $boleteriaController = new BoleteriaController();
        $boleteriaController->procesarVenta();
        break;

    case 'apertura-caja':
        $cajaController = new CajaController();
        $cajaController->apertura();
        break;

    case 'procesar-apertura-caja':
        $cajaController = new CajaController();
        $cajaController->procesarApertura();
        break;

    case 'cierre-caja':
        $cajaController = new CajaController();
        $cajaController->cierre();
        break;

    case 'dulceria':
        $dulceriaCtrl = new DulceriaController();
        $dulceriaCtrl->index();
        break;

    case 'procesar-venta-dulceria':
        $dulceriaCtrl = new DulceriaController();
        $dulceriaCtrl->procesarVenta();
        break;

    case 'procesar-cierre-caja':
        $cajaController = new CajaController();
        $cajaController->procesarCierre();
        break;

    // Rutas de Administración
    case 'admin':
    case 'admin-dashboard':
        $adminCtrl = new AdminController();
        $adminCtrl->dashboard();
        break;

    case 'admin-peliculas':
        $adminCtrl = new AdminController();
        $adminCtrl->peliculas();
        break;

    case 'admin-guardar-pelicula':
        $adminCtrl = new AdminController();
        $adminCtrl->guardarPelicula();
        break;

    case 'admin-eliminar-pelicula':
        $adminCtrl = new AdminController();
        $adminCtrl->eliminarPelicula();
        break;

    case 'admin-salas':
        $adminCtrl = new AdminController();
        $adminCtrl->salas();
        break;

    case 'admin-guardar-sala':
        $adminCtrl = new AdminController();
        $adminCtrl->guardarSala();
        break;

    case 'admin-eliminar-sala':
        $adminCtrl = new AdminController();
        $adminCtrl->eliminarSala();
        break;

    case 'admin-funciones':
        $adminCtrl = new AdminController();
        $adminCtrl->funciones();
        break;

    case 'admin-guardar-funcion':
        $adminCtrl = new AdminController();
        $adminCtrl->guardarFuncion();
        break;

    case 'admin-eliminar-funcion':
        $adminCtrl = new AdminController();
        $adminCtrl->eliminarFuncion();
        break;

    case 'admin-usuarios':
        $adminCtrl = new AdminController();
        $adminCtrl->usuarios();
        break;

    case 'admin-guardar-usuario':
        $adminCtrl = new AdminController();
        $adminCtrl->guardarUsuario();
        break;

    case 'admin-toggle-usuario':
        $adminCtrl = new AdminController();
        $adminCtrl->toggleEstadoUsuario();
        break;

    case 'admin-cortes':
        $adminCtrl = new AdminController();
        $adminCtrl->cortesCaja();
        break;

    case 'admin-detalle-corte':
        $adminCtrl = new AdminController();
        $adminCtrl->detalleCorteJson();
        break;

    case 'admin-productos':
        $adminCtrl = new AdminController();
        $adminCtrl->productos();
        break;

    case 'admin-guardar-producto':
        $adminCtrl = new AdminController();
        $adminCtrl->guardarProducto();
        break;

    case 'admin-toggle-producto':
        $adminCtrl = new AdminController();
        $adminCtrl->toggleEstadoProducto();
        break;

    case 'admin-eliminar-producto':
        $adminCtrl = new AdminController();
        $adminCtrl->eliminarProducto();
        break;

    case 'admin-reportes':
        $adminCtrl = new AdminController();
        $adminCtrl->reportes();
        break;

    case 'admin-reporte-pdf':
        $adminCtrl = new AdminController();
        $adminCtrl->reportePdf();
        break;

    case 'login':
    default:
        $authController->showLogin();
        break;
}
