<?php

require_once __DIR__ . '/../models/Pelicula.php';
require_once __DIR__ . '/../models/Sala.php';
require_once __DIR__ . '/../models/Funcion.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Caja.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Venta.php';

class AdminController
{
    private function verificarAdmin(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $idRol = (int)($_SESSION['id_rol'] ?? 0);
        $nombreRol = strtolower(trim($_SESSION['nombre_rol'] ?? ''));

        if ($idRol !== 1 && strpos($nombreRol, 'admin') === false) {
            $_SESSION['error_acceso'] = 'Acceso denegado: este módulo requiere permisos de Administrador.';
            header('Location: index.php?action=boleteria');
            exit;
        }
    }

    // Dashboard principal de administracion
    public function dashboard(): void
    {
        $this->verificarAdmin();

        $modeloPelicula = new Pelicula();
        $modeloSala     = new Sala();
        $modeloFuncion  = new Funcion();
        $modeloUsuario  = new Usuario();
        $modeloProducto = new Producto();

        $peliculas = $modeloPelicula->listarTodas();
        $salas     = $modeloSala->listarTodas();
        $funciones = $modeloFuncion->listarTodas();
        $usuarios  = $modeloUsuario->listarTodos();

        $totalPeliculas = count($peliculas);
        $totalSalas     = count($salas);
        $totalFunciones = count($funciones);
        $totalUsuarios  = count($usuarios);
        $totalProductos = $modeloProducto->contarTotal();

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        $mensajeExito = $_SESSION['admin_success'] ?? null;
        $mensajeError = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    // Gestion de peliculas
    public function peliculas(): void
    {
        $this->verificarAdmin();

        $modelo = new Pelicula();
        $peliculas = $modelo->listarTodas();

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        $mensajeExito = $_SESSION['admin_success'] ?? null;
        $mensajeError = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require_once __DIR__ . '/../views/admin/peliculas.php';
    }

    public function guardarPelicula(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin-peliculas');
            exit;
        }

        $id            = (int)($_POST['id_pelicula'] ?? 0);
        $titulo        = trim($_POST['titulo'] ?? '');
        $clasificacion = trim($_POST['clasificacion'] ?? 'B');
        $duracion      = (int)($_POST['duracion'] ?? 0);
        $imagenActual  = trim($_POST['imagen_actual'] ?? '');

        if ($titulo === '' || $duracion <= 0) {
            $_SESSION['admin_error'] = 'Por favor complete todos los datos obligatorios de la película.';
            header('Location: index.php?action=admin-peliculas');
            exit;
        }

        // Subida de afiche
        $nombreImagen = $imagenActual;
        if (!empty($_FILES['imagen']['name'])) {
            if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $archivoTmp    = $_FILES['imagen']['tmp_name'];
                $nombreOrig    = $_FILES['imagen']['name'];
                $ext           = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));
                $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($ext, $extPermitidas, true)) {
                    $nuevoNombre = 'poster_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $destino = __DIR__ . '/../img/' . $nuevoNombre;
                    if (move_uploaded_file($archivoTmp, $destino)) {
                        $nombreImagen = $nuevoNombre;
                    }
                } else {
                    $_SESSION['admin_error'] = 'Formato de imagen no permitido. Solo se permiten JPG, PNG o WEBP.';
                    header('Location: index.php?action=admin-peliculas');
                    exit;
                }
            } else {
                $_SESSION['admin_error'] = 'Error al cargar el archivo de imagen. Verifique el tamaño.';
                header('Location: index.php?action=admin-peliculas');
                exit;
            }
        }

        $modelo = new Pelicula();
        if ($id > 0) {
            $modelo->actualizar($id, $titulo, $clasificacion, $duracion, $nombreImagen);
            $_SESSION['admin_success'] = 'Película "' . $titulo . '" actualizada correctamente.';
        } else {
            $modelo->crear($titulo, $clasificacion, $duracion, $nombreImagen);
            $_SESSION['admin_success'] = 'Película "' . $titulo . '" agregada a la cartelera.';
        }

        header('Location: index.php?action=admin-peliculas');
        exit;
    }

    public function eliminarPelicula(): void
    {
        $this->verificarAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?action=admin-peliculas');
            exit;
        }

        $modelo = new Pelicula();
        if ($modelo->tieneBoletosVendidos($id)) {
            $_SESSION['admin_error'] = 'No se puede eliminar la película porque tiene funciones con boletos ya vendidos en taquilla.';
            header('Location: index.php?action=admin-peliculas');
            exit;
        }

        $modelo->eliminar($id);
        $_SESSION['admin_success'] = 'Película eliminada correctamente.';
        header('Location: index.php?action=admin-peliculas');
        exit;
    }

    // Gestion de salas
    public function salas(): void
    {
        $this->verificarAdmin();

        $modelo = new Sala();
        $salas  = $modelo->listarTodas();

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        $mensajeExito = $_SESSION['admin_success'] ?? null;
        $mensajeError = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require_once __DIR__ . '/../views/admin/salas.php';
    }

    public function guardarSala(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin-salas');
            exit;
        }

        $id        = (int)($_POST['id_sala'] ?? 0);
        $nombre    = trim($_POST['nombre_sala'] ?? '');
        $capacidad = (int)($_POST['capacidad'] ?? 110);

        if ($nombre === '' || $capacidad <= 0) {
            $_SESSION['admin_error'] = 'Debe indicar un nombre válido y capacidad mayor a 0.';
            header('Location: index.php?action=admin-salas');
            exit;
        }

        $modelo = new Sala();
        if ($id > 0) {
            $modelo->actualizar($id, $nombre, $capacidad);
            $_SESSION['admin_success'] = 'Sala actualizada correctamente.';
        } else {
            $modelo->crear($nombre, $capacidad);
            $_SESSION['admin_success'] = 'Nueva sala registrada exitosamente.';
        }

        header('Location: index.php?action=admin-salas');
        exit;
    }

    public function eliminarSala(): void
    {
        $this->verificarAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?action=admin-salas');
            exit;
        }

        $modelo = new Sala();
        if ($modelo->tieneBoletosVendidos($id)) {
            $_SESSION['admin_error'] = 'No se puede eliminar la sala porque tiene funciones con boletos ya vendidos en taquilla.';
            header('Location: index.php?action=admin-salas');
            exit;
        }

        $modelo->eliminar($id);
        $_SESSION['admin_success'] = 'Sala eliminada correctamente.';
        header('Location: index.php?action=admin-salas');
        exit;
    }

    // Gestion de funciones y horarios
    public function funciones(): void
    {
        $this->verificarAdmin();

        $modeloFuncion  = new Funcion();
        $modeloPelicula = new Pelicula();
        $modeloSala     = new Sala();

        $funciones = $modeloFuncion->listarTodas();
        $peliculas = $modeloPelicula->listarTodas();
        $salas     = $modeloSala->listarTodas();

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        $mensajeExito = $_SESSION['admin_success'] ?? null;
        $mensajeError = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require_once __DIR__ . '/../views/admin/funciones.php';
    }

    public function guardarFuncion(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin-funciones');
            exit;
        }

        $id         = (int)($_POST['id_funcion'] ?? 0);
        $idPelicula = (int)($_POST['id_pelicula'] ?? 0);
        $idSala     = (int)($_POST['id_sala'] ?? 0);
        $fecha      = trim($_POST['fecha'] ?? '');
        $precio     = (float)($_POST['precio_boleto'] ?? 4.00);
        $formato    = trim($_POST['formato'] ?? '2D');

        // Soporte para múltiples horarios en creación o uno solo en edición
        $horasInput = $_POST['horas'] ?? [$_POST['hora'] ?? ''];
        if (is_string($horasInput)) {
            $horasInput = array_map('trim', explode(',', $horasInput));
        }

        $horasValidas = array_values(array_unique(array_filter(array_map('trim', (array)$horasInput), fn($h) => $h !== '')));

        if ($idPelicula <= 0 || $idSala <= 0 || $fecha === '' || empty($horasValidas) || $precio <= 0) {
            $_SESSION['admin_error'] = 'Complete todos los campos obligatorios y defina al menos un horario.';
            header('Location: index.php?action=admin-funciones');
            exit;
        }

        $modeloPelicula = new Pelicula();
        $pelicula = $modeloPelicula->obtenerPorId($idPelicula);
        if (!$pelicula) {
            $_SESSION['admin_error'] = 'La película seleccionada no existe en el catálogo.';
            header('Location: index.php?action=admin-funciones');
            exit;
        }

        $duracionPelicula = (int)($pelicula['duracion'] ?? 120);
        if ($duracionPelicula <= 0) {
            $duracionPelicula = 120;
        }

        // Ordenar cronológicamente los horarios seleccionados
        usort($horasValidas, fn($a, $b) => strcmp($a, $b));

        // 1. Validar solapamiento interno entre los horarios seleccionados en este mismo envío
        $prevFinTimestamp = null;
        $prevHoraStr = '';
        foreach ($horasValidas as $hora) {
            $inicioTs = strtotime($fecha . ' ' . $hora . ':00');
            if ($prevFinTimestamp !== null && $inicioTs < $prevFinTimestamp) {
                $hIni = date('h:i A', $inicioTs);
                $hFinPrev = date('h:i A', $prevFinTimestamp);
                $_SESSION['admin_error'] = "Conflicto entre los horarios seleccionados: la tanda de las {$prevHoraStr} finaliza a las {$hFinPrev} (duración: {$duracionPelicula} min) y se solapa con la tanda de las {$hIni}.";
                header('Location: index.php?action=admin-funciones');
                exit;
            }
            $prevFinTimestamp = $inicioTs + ($duracionPelicula * 60);
            $prevHoraStr = date('h:i A', $inicioTs);
        }

        $modelo = new Funcion();

        // 2. Validar que ninguna de las funciones a crear/editar se solape con funciones ya existentes en la sala
        foreach ($horasValidas as $hora) {
            $fechaHora = $fecha . ' ' . $hora . ':00';
            $conflicto = $modelo->verificarSolapamientoHorario($idSala, $fechaHora, $duracionPelicula, $id);
            if ($conflicto !== null) {
                $horaNuevaIni = date('h:i A', strtotime($fechaHora));
                $horaNuevaFin = date('h:i A', strtotime($fechaHora) + ($duracionPelicula * 60));
                $horaConfIni  = date('h:i A', strtotime($conflicto['fecha_hora']));
                $horaConfFin  = date('h:i A', strtotime($conflicto['fecha_hora_fin']));
                $peliConf     = htmlspecialchars($conflicto['pelicula_titulo']);
                $salaConf     = htmlspecialchars($conflicto['nombre_sala']);

                $_SESSION['admin_error'] = "Conflicto en {$salaConf}: el horario {$horaNuevaIni} a {$horaNuevaFin} se solapa con '{$peliConf}' programada de {$horaConfIni} a {$horaConfFin}.";
                header('Location: index.php?action=admin-funciones');
                exit;
            }
        }

        if ($id > 0) {
            // Actualiza una funcion existente
            $horaUnica = reset($horasValidas);
            $fechaHora = $fecha . ' ' . $horaUnica . ':00';
            $modelo->actualizar($id, $idPelicula, $idSala, $fechaHora, $precio, $formato);
            $_SESSION['admin_success'] = 'Función actualizada correctamente.';
        } else {
            // Inserta cada horario seleccionado
            $insertadas = 0;
            foreach ($horasValidas as $hora) {
                $fechaHora = $fecha . ' ' . $hora . ':00';
                $modelo->crear($idPelicula, $idSala, $fechaHora, $precio, $formato);
                $insertadas++;
            }
            $_SESSION['admin_success'] = "Se programaron {$insertadas} tanda(s)/horario(s) exitosamente.";
        }

        header('Location: index.php?action=admin-funciones');
        exit;
    }

    public function eliminarFuncion(): void
    {
        $this->verificarAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?action=admin-funciones');
            exit;
        }

        $modelo = new Funcion();
        if ($modelo->tieneBoletosVendidos($id)) {
            $_SESSION['admin_error'] = 'No se puede eliminar esta función porque ya tiene boletos vendidos.';
            header('Location: index.php?action=admin-funciones');
            exit;
        }

        $modelo->eliminar($id);
        $_SESSION['admin_success'] = 'Función eliminada de la cartelera.';
        header('Location: index.php?action=admin-funciones');
        exit;
    }

    // Gestion de usuarios y accesos
    public function usuarios(): void
    {
        $this->verificarAdmin();

        $modelo = new Usuario();
        $usuarios = $modelo->listarTodos();
        $roles    = $modelo->listarRoles();

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';
        $idAdminActual = (int)($_SESSION['id_usuario'] ?? 0);

        $mensajeExito = $_SESSION['admin_success'] ?? null;
        $mensajeError = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require_once __DIR__ . '/../views/admin/usuarios.php';
    }

    public function guardarUsuario(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin-usuarios');
            exit;
        }

        $id       = (int)($_POST['id_usuario'] ?? 0);
        $nombre   = trim($_POST['nombre'] ?? '');
        $usuario  = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $idRol    = (int)($_POST['id_rol'] ?? 2);

        if ($nombre === '' || $usuario === '' || $idRol <= 0) {
            $_SESSION['admin_error'] = 'Por favor complete el nombre, usuario y rol.';
            header('Location: index.php?action=admin-usuarios');
            exit;
        }

        $modelo = new Usuario();
        if ($modelo->existeUsuario($usuario, $id)) {
            $_SESSION['admin_error'] = 'El nombre de usuario "' . $usuario . '" ya se encuentra registrado.';
            header('Location: index.php?action=admin-usuarios');
            exit;
        }

        if ($id > 0) {
            $modelo->actualizar($id, $nombre, $usuario, $idRol);
            if ($password !== '') {
                $modelo->cambiarPassword($id, $password);
            }
            $_SESSION['admin_success'] = 'Usuario "' . $usuario . '" actualizado.';
        } else {
            if ($password === '') {
                $_SESSION['admin_error'] = 'Debe definir una contraseña inicial para el nuevo usuario.';
                header('Location: index.php?action=admin-usuarios');
                exit;
            }
            $modelo->crear($nombre, $usuario, $password, $idRol);
            $_SESSION['admin_success'] = 'Usuario "' . $usuario . '" creado exitosamente.';
        }

        header('Location: index.php?action=admin-usuarios');
        exit;
    }

    public function toggleEstadoUsuario(): void
    {
        $this->verificarAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $idAdminActual = (int)($_SESSION['id_usuario'] ?? 0);

        if ($id <= 0 || $id === $idAdminActual) {
            $_SESSION['admin_error'] = 'No puede desactivar su propio usuario en sesión.';
            header('Location: index.php?action=admin-usuarios');
            exit;
        }

        $modelo = new Usuario();
        $u = $modelo->obtenerPorId($id);
        if ($u) {
            $nuevoEstado = ($u['estado'] === 'Activo' || (int)$u['estado'] === 1) ? 'Inactivo' : 'Activo';
            $modelo->cambiarEstado($id, $nuevoEstado);
            $_SESSION['admin_success'] = 'Estado de ' . $u['nombre'] . ' cambiado a: ' . $nuevoEstado;
        }

        header('Location: index.php?action=admin-usuarios');
        exit;
    }

    // Reporte general de cortes y cuadres de caja
    public function cortesCaja(): void
    {
        $this->verificarAdmin();

        $fechaFiltro   = trim($_GET['fecha'] ?? '');
        $usuarioFiltro = (int)($_GET['id_usuario'] ?? 0);

        $modeloCaja    = new Caja();
        $modeloUsuario = new Usuario();

        $cortes   = $modeloCaja->listarTodosLosCortes($fechaFiltro !== '' ? $fechaFiltro : null, $usuarioFiltro > 0 ? $usuarioFiltro : null);
        $usuarios = $modeloUsuario->listarTodos();

        $totalCortesCerrados = 0;
        $totalRecaudado      = 0.0;
        $totalFaltantes      = 0.0;
        $totalSobrantes      = 0.0;
        $conteoFaltantes     = 0;
        $conteoSobrantes     = 0;

        foreach ($cortes as $c) {
            if (!empty($c['fecha_cierre'])) {
                $totalCortesCerrados++;
                $totalRecaudado += (float)$c['total_ventas'];
                $dif = (float)$c['diferencia'];
                if ($dif < 0) {
                    $conteoFaltantes++;
                    $totalFaltantes += abs($dif);
                } elseif ($dif > 0) {
                    $conteoSobrantes++;
                    $totalSobrantes += $dif;
                }
            }
        }

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        require_once __DIR__ . '/../views/admin/cortes.php';
    }

    // Retorna informacion detallada de un corte en formato JSON para el modal
    public function detalleCorteJson(): void
    {
        $this->verificarAdmin();

        $idCorte = (int)($_GET['id'] ?? 0);
        $modeloCaja = new Caja();
        $detalle = $modeloCaja->obtenerDetalleCompletoCorte($idCorte);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($detalle ?: ['error' => 'Corte no encontrado']);
        exit;
    }

    // ==========================================
    // GESTIÓN DE PRODUCTOS Y DULCERÍA (CRUD)
    // ==========================================

    public function productos(): void
    {
        $this->verificarAdmin();

        $categoriaFiltro = trim($_GET['categoria'] ?? '');
        $busquedaFiltro  = trim($_GET['buscar'] ?? '');

        $modeloProducto = new Producto();
        $productos  = $modeloProducto->listarTodos(
            $categoriaFiltro !== '' ? $categoriaFiltro : null,
            $busquedaFiltro !== '' ? $busquedaFiltro : null
        );
        $categorias = $modeloProducto->obtenerCategorias();

        $totalProductos = count($productos);
        $totalStock     = 0;
        $totalAgotados  = 0;

        foreach ($productos as $p) {
            $totalStock += (int)$p['stock_disponible'];
            if ((int)$p['stock_disponible'] <= 0) {
                $totalAgotados++;
            }
        }

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        $mensajeExito = $_SESSION['admin_success'] ?? null;
        $mensajeError = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        require_once __DIR__ . '/../views/admin/productos.php';
    }

    public function guardarProducto(): void
    {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin-productos');
            exit;
        }

        $id              = (int)($_POST['id_producto'] ?? 0);
        $nombre          = trim($_POST['nombre'] ?? '');
        $categoria       = trim($_POST['categoria'] ?? 'palomitas');
        $descripcion     = trim($_POST['descripcion'] ?? '');
        $precio          = (float)($_POST['precio'] ?? 0.0);
        $stockDisponible = (int)($_POST['stock_disponible'] ?? 0);
        $personalizable  = isset($_POST['personalizable']) ? 1 : 0;
        $activo          = isset($_POST['activo']) ? 1 : 0;
        $imagenActual    = trim($_POST['imagen_actual'] ?? '');
        $imagenSugerida  = trim($_POST['imagen_sugerida'] ?? '');

        if ($nombre === '' || $precio <= 0 || $categoria === '') {
            $_SESSION['admin_error'] = 'El nombre, la categoría y el precio (mayor a cero) son campos obligatorios.';
            header('Location: index.php?action=admin-productos');
            exit;
        }

        // Manejo de imagen: 1) Archivo subido, 2) Imagen predefinida seleccionada, 3) Imagen actual existente
        $nombreImagen = $imagenActual !== '' ? $imagenActual : ($imagenSugerida !== '' ? $imagenSugerida : null);

        if (!empty($_FILES['imagen']['name'])) {
            if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $archivoTmp    = $_FILES['imagen']['tmp_name'];
                $nombreOrig    = $_FILES['imagen']['name'];
                $ext           = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));
                $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($ext, $extPermitidas, true)) {
                    $carpetaDestino = __DIR__ . '/../img/dulceria/';
                    if (!is_dir($carpetaDestino)) {
                        mkdir($carpetaDestino, 0777, true);
                    }
                    $nuevoNombre = 'prod_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                    $destino = $carpetaDestino . $nuevoNombre;
                    if (move_uploaded_file($archivoTmp, $destino)) {
                        $nombreImagen = 'img/dulceria/' . $nuevoNombre;
                    }
                } else {
                    $_SESSION['admin_error'] = 'Formato de imagen no válido. Use JPG, PNG o WEBP.';
                    header('Location: index.php?action=admin-productos');
                    exit;
                }
            }
        } elseif (!empty($_POST['imagen_sugerida'])) {
            $nombreImagen = trim($_POST['imagen_sugerida']);
        }

        $modelo = new Producto();
        if ($id > 0) {
            $ok = $modelo->actualizar($id, $nombre, $categoria, $descripcion, $precio, $nombreImagen, $personalizable, $stockDisponible, $activo);
            if ($ok) {
                $_SESSION['admin_success'] = 'Producto "' . htmlspecialchars($nombre) . '" actualizado correctamente.';
            } else {
                $_SESSION['admin_error'] = 'No se pudo actualizar el producto.';
            }
        } else {
            $nuevoId = $modelo->crear($nombre, $categoria, $descripcion, $precio, $nombreImagen, $personalizable, $stockDisponible, $activo);
            if ($nuevoId) {
                $_SESSION['admin_success'] = 'Producto "' . htmlspecialchars($nombre) . '" creado e incorporado a la dulcería con ID #' . $nuevoId . '.';
            } else {
                $_SESSION['admin_error'] = 'Ocurrió un error al crear el nuevo producto.';
            }
        }

        header('Location: index.php?action=admin-productos');
        exit;
    }

    public function toggleEstadoProducto(): void
    {
        $this->verificarAdmin();

        $id = (int)($_POST['id_producto'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $modelo = new Producto();
            $modelo->toggleEstado($id);
            $_SESSION['admin_success'] = 'Estado del producto actualizado correctamente.';
        }

        header('Location: index.php?action=admin-productos');
        exit;
    }

    public function eliminarProducto(): void
    {
        $this->verificarAdmin();

        $id = (int)($_POST['id_producto'] ?? $_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?action=admin-productos');
            exit;
        }

        $modelo = new Producto();
        $res = $modelo->eliminar($id);

        if ($res['accion'] === 'eliminado') {
            $_SESSION['admin_success'] = $res['mensaje'];
        } else {
            $_SESSION['admin_success'] = $res['mensaje'];
        }

        header('Location: index.php?action=admin-productos');
        exit;
    }

    /**
     * Muestra la interfaz de reportes administrativos y consulta de datos.
     */
    public function reportes(): void
    {
        $this->verificarAdmin();

        $tipo = trim($_GET['tipo'] ?? 'ventas');
        $tiposPermitidos = ['ventas', 'dulceria', 'cortes', 'inventario'];
        if (!in_array($tipo, $tiposPermitidos, true)) {
            $tipo = 'ventas';
        }

        $fechaInicio = trim($_GET['fecha_inicio'] ?? date('Y-m-01'));
        $fechaFin    = trim($_GET['fecha_fin'] ?? date('Y-m-d'));
        $idUsuario   = (int)($_GET['id_usuario'] ?? 0);

        $modeloUsuario  = new Usuario();
        $modeloVenta    = new Venta();
        $modeloProducto = new Producto();
        $modeloCaja     = new Caja();

        $usuarios = $modeloUsuario->listarTodos();

        $datosReporte = [];

        switch ($tipo) {
            case 'ventas':
                $datosReporte = $modeloVenta->generarReporteVentas(
                    $fechaInicio !== '' ? $fechaInicio : null,
                    $fechaFin !== '' ? $fechaFin : null,
                    $idUsuario > 0 ? $idUsuario : null
                );
                break;

            case 'dulceria':
                $datosReporte = $modeloVenta->generarReporteRankingDulceria(
                    $fechaInicio !== '' ? $fechaInicio : null,
                    $fechaFin !== '' ? $fechaFin : null
                );
                break;

            case 'cortes':
                $datosReporte = $modeloCaja->generarReporteConsolidadoCortes(
                    $fechaInicio !== '' ? $fechaInicio : null,
                    $fechaFin !== '' ? $fechaFin : null,
                    $idUsuario > 0 ? $idUsuario : null
                );
                break;

            case 'inventario':
                $datosReporte = $modeloProducto->generarReporteInventario();
                break;
        }

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        require_once __DIR__ . '/../views/admin/reportes.php';
    }

    /**
     * Genera y descarga el reporte en formato PDF oficial utilizando Dompdf vía Composer.
     */
    public function reportePdf(): void
    {
        $this->verificarAdmin();

        $tipo = trim($_GET['tipo'] ?? 'ventas');
        $tiposPermitidos = ['ventas', 'dulceria', 'cortes', 'inventario'];
        if (!in_array($tipo, $tiposPermitidos, true)) {
            $tipo = 'ventas';
        }

        $fechaInicio = trim($_GET['fecha_inicio'] ?? date('Y-m-01'));
        $fechaFin    = trim($_GET['fecha_fin'] ?? date('Y-m-d'));
        $idUsuario   = (int)($_GET['id_usuario'] ?? 0);

        $modeloUsuario  = new Usuario();
        $modeloVenta    = new Venta();
        $modeloProducto = new Producto();
        $modeloCaja     = new Caja();

        $usuarios = $modeloUsuario->listarTodos();
        $datosReporte = [];

        switch ($tipo) {
            case 'ventas':
                $datosReporte = $modeloVenta->generarReporteVentas(
                    $fechaInicio !== '' ? $fechaInicio : null,
                    $fechaFin !== '' ? $fechaFin : null,
                    $idUsuario > 0 ? $idUsuario : null
                );
                break;

            case 'dulceria':
                $datosReporte = $modeloVenta->generarReporteRankingDulceria(
                    $fechaInicio !== '' ? $fechaInicio : null,
                    $fechaFin !== '' ? $fechaFin : null
                );
                break;

            case 'cortes':
                $datosReporte = $modeloCaja->generarReporteConsolidadoCortes(
                    $fechaInicio !== '' ? $fechaInicio : null,
                    $fechaFin !== '' ? $fechaFin : null,
                    $idUsuario > 0 ? $idUsuario : null
                );
                break;

            case 'inventario':
                $datosReporte = $modeloProducto->generarReporteInventario();
                break;
        }

        $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';
        $nombreRol   = $_SESSION['nombre_rol'] ?? 'Administrador';

        require_once __DIR__ . '/../services/ReportePdfService.php';
        $pdfService = new \Services\ReportePdfService();
        $pdfService->generar(
            $tipo,
            $datosReporte,
            $fechaInicio,
            $fechaFin,
            $idUsuario,
            $usuarios,
            $nombreAdmin,
            $nombreRol
        );
    }
}
