<?php
/** @var string $nombreCajero */
/** @var string $nombreRol */
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRol = $nombreRol ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin Turno Abierto — Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/boleteria.css" rel="stylesheet">
</head>
<body class="pos-body">

    <!-- Header Principal -->
    <header class="pos-header">
        <div class="pos-brand">
            <img src="img/cinestarklogo.png" alt="Cine Stark" class="pos-logo-img">
            <h1 class="pos-title">CIERRE DE CAJA</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRol) ?>: <strong class="pos-user-name"><?= htmlspecialchars($nombreCajero) ?></strong></span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Toolbar de Navegación -->
    <nav class="pos-toolbar" aria-label="Navegación de módulos">
        <div class="pos-nav-tabs">
            <a href="index.php?action=boleteria" class="pos-pill">Boletería</a>
            <a href="index.php?action=dulceria" class="pos-pill">Dulcería</a>
            <a href="index.php?action=cierre-caja" class="pos-pill active">Cierre de caja</a>
            <?php if (isset($_SESSION['id_rol']) && ((int)$_SESSION['id_rol'] === 1 || stripos($_SESSION['nombre_rol'] ?? '', 'admin') !== false)): ?>
                <a href="index.php?action=admin-dashboard" class="pos-pill" style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-gear-fill me-1"></i> Administración</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="pos-content d-flex align-items-center justify-content-center" style="min-height: 75vh;">
        <div class="login-card text-center" style="max-width: 440px; width: 100%; padding: 2.5rem;">
            <i class="bi bi-info-circle fs-1 text-dorado mb-2 d-block"></i>
            <h2 class="section-headline mb-2" style="font-size: 1.8rem;">SIN TURNO ABIERTO</h2>
            <p class="text-white-50 small mb-4">
                No tienes un turno de caja abierto en este momento para realizar un corte.
            </p>
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="index.php?action=apertura-caja" class="btn-cobrar-stark px-4 text-decoration-none d-inline-block" style="margin-top: 0; width: auto;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Abrir Turno
                </a>
                <a href="index.php?action=boleteria" class="btn btn-outline-light px-4 py-2">
                    Ir a Boletería
                </a>
                <?php if (isset($_SESSION['id_rol']) && ((int)$_SESSION['id_rol'] === 1 || stripos($_SESSION['nombre_rol'] ?? '', 'admin') !== false)): ?>
                    <a href="index.php?action=admin-dashboard" class="btn btn-outline-warning px-4 py-2">
                        <i class="bi bi-gear-fill me-1"></i> Administración
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
