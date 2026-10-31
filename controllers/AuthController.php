<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    public function showLogin(): void
    {
        if (!empty($_SESSION['id_usuario'])) {
            $idRol = (int)($_SESSION['id_rol'] ?? 0);
            $nombreRol = strtolower(trim($_SESSION['nombre_rol'] ?? ''));
            $esAdmin = ($idRol === 1 || strpos($nombreRol, 'admin') !== false);

            if ($esAdmin) {
                header('Location: index.php?action=admin-dashboard');
                exit;
            }

            header('Location: index.php?action=boleteria');
            exit;
        }

        $error   = $_SESSION['login_error'] ?? null;
        $success = $_SESSION['login_success'] ?? null;
        unset($_SESSION['login_error'], $_SESSION['login_success']);

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit;
        }

        $usuario  = trim(filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $password = $_POST['password'] ?? '';

        if ($usuario === '' || $password === '') {
            $_SESSION['login_error'] = 'Complete todos los campos.';
            header('Location: index.php?action=login');
            exit;
        }

        $modelo = new Usuario();
        $datos  = $modelo->autenticar($usuario, $password);

        if ($datos === false) {
            $_SESSION['login_error'] = 'Credenciales incorrectas o cuenta inactiva.';
            header('Location: index.php?action=login');
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['id_usuario'] = $datos['id_usuario'];
        $_SESSION['nombre']     = $datos['nombre'];
        $_SESSION['id_rol']     = $datos['id_rol'];
        $_SESSION['nombre_rol'] = $datos['nombre_rol'];

        $idRol = (int)$datos['id_rol'];
        $nombreRol = strtolower(trim($datos['nombre_rol'] ?? ''));
        $esAdmin = ($idRol === 1 || strpos($nombreRol, 'admin') !== false);

        if ($esAdmin) {
            header('Location: index.php?action=admin-dashboard');
            exit;
        }

        // Valida si el cajero ya tiene un turno abierto
        require_once __DIR__ . '/../models/Caja.php';
        $modeloCaja = new Caja();
        $turnoAbierto = $modeloCaja->obtenerTurnoAbierto((int)$datos['id_usuario']);

        if (!$turnoAbierto) {
            header('Location: index.php?action=apertura-caja');
            exit;
        }

        header('Location: index.php?action=boleteria');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }

        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
