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
    <title>Apertura de Turno de Caja — Cine Stark</title>

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
            <h1 class="pos-title">CONTROL DE CAJA</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRol) ?>: <strong class="pos-user-name"><?= htmlspecialchars($nombreCajero) ?></strong> · <span class="text-warning">Sin turno activo</span></span>
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
            <a href="index.php?action=cierre-caja" class="pos-pill">Cierre de caja</a>
            <?php if (isset($_SESSION['id_rol']) && ((int)$_SESSION['id_rol'] === 1 || stripos($_SESSION['nombre_rol'] ?? '', 'admin') !== false)): ?>
                <a href="index.php?action=admin-dashboard" class="pos-pill" style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-gear-fill me-1"></i> Administración</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="pos-content d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="login-card text-center" style="max-width: 440px; width: 100%; padding: 2.5rem;">
            
            <i class="bi bi-cash-coin fs-1 text-dorado mb-2 d-block"></i>
            <h2 class="section-headline mb-1" style="font-size: 1.9rem;">APERTURA DE TURNO</h2>
            <p class="text-white-50 small mb-4">
                Registre el fondo inicial en efectivo disponible en la gaveta para iniciar operaciones.
            </p>

            <?php if (!empty($mensajeAviso)): ?>
                <div class="alert alert-warning text-start py-2 px-3 mb-3 small" style="background-color: rgba(245, 185, 33, 0.15); border: 1px solid var(--cs-dorado); color: var(--cs-dorado);">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($mensajeAviso) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?action=procesar-apertura-caja" method="POST">
                <div class="mb-4 text-start">
                    <label for="inputFondoInicial" class="form-label-stark">Fondo Inicial en Gaveta ($)</label>
                    <div class="input-moneda-wrapper">
                        <span class="input-moneda-simbolo">$</span>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control input-efectivo"
                            id="inputFondoInicial"
                            name="monto_inicial"
                            value="50.00"
                            required
                            autofocus
                        >
                    </div>
                    <small class="text-white-50 mt-1 d-block">Monto estándar de cambio para inicio de turno.</small>
                </div>

                <button type="submit" class="btn-cobrar-stark w-100" style="margin-top: 0;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> ABRIR TURNO Y COMENZAR
                </button>
            </form>

            <?php if (isset($_SESSION['id_rol']) && ((int)$_SESSION['id_rol'] === 1 || stripos($_SESSION['nombre_rol'] ?? '', 'admin') !== false)): ?>
                <div class="mt-3">
                    <a href="index.php?action=admin-dashboard" class="btn btn-outline-warning w-100">
                        <i class="bi bi-gear-fill me-1"></i> Ir al Panel de Administración
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
