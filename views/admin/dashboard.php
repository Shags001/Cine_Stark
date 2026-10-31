<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var int $totalPeliculas */
/** @var int $totalSalas */
/** @var int $totalFunciones */
/** @var int $totalUsuarios */
/** @var int $totalProductos */
/** @var array $peliculas */
/** @var array $funciones */
/** @var string|null $mensajeExito */
/** @var string|null $mensajeError */
$nombreAdmin = $nombreAdmin ?? 'Administrador';
$nombreRol = $nombreRol ?? 'Administrador';
$totalProductos = $totalProductos ?? 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/boleteria.css" rel="stylesheet">
</head>

<body class="pos-body">

    <!-- Header Principal -->
    <header class="pos-header">
        <div class="pos-brand">
            <img src="img/cinestarklogo.png" alt="Cine Stark" class="pos-logo-img">
            <h1 class="pos-title">PANEL DE CONTROL · ADMINISTRACIÓN</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRol) ?>: <strong
                    class="pos-user-name"><?= htmlspecialchars($nombreAdmin) ?></strong></span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Toolbar de Navegación Admin -->
    <nav class="pos-toolbar" aria-label="Navegación del Administrador">
        <div class="pos-nav-tabs">
            <a href="index.php?action=admin-dashboard" class="pos-pill active"><i class="bi bi-speedometer2 me-1"></i>
                Resumen</a>
            <a href="index.php?action=admin-peliculas" class="pos-pill"><i class="bi bi-film me-1"></i> Películas</a>
            <a href="index.php?action=admin-salas" class="pos-pill"><i class="bi bi-door-open me-1"></i> Salas</a>
            <a href="index.php?action=admin-funciones" class="pos-pill"><i class="bi bi-calendar2-week me-1"></i>
                Cartelera</a>
            <a href="index.php?action=admin-usuarios" class="pos-pill"><i class="bi bi-people me-1"></i> Usuarios</a>
            <a href="index.php?action=admin-cortes" class="pos-pill"><i class="bi bi-cash-stack me-1"></i> Cortes de
                Caja</a>
            <a href="index.php?action=admin-productos" class="pos-pill"><i class="bi bi-cup-straw me-1"></i>
                Dulcería</a>
            <a href="index.php?action=admin-reportes" class="pos-pill"><i class="bi bi-file-earmark-bar-graph me-1"></i> Reportes</a>
            <a href="index.php?action=boleteria" class="pos-pill"
                style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i
                    class="bi bi-ticket-perforated me-1"></i> Ir a Boletería</a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="pos-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-headline mb-0">ESTADO GENERAL DEL COMPLEJO</h2>
            <span class="badge"
                style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde); color: var(--cs-blanco-suave); font-size: 0.85rem; padding: 0.5rem 1rem;">
                3 Salas · 330 Butacas Totales
            </span>
        </div>

        <?php if (!empty($mensajeExito)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981;">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($mensajeExito) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert"
            style="background-color: rgba(217, 30, 24, 0.15); border: 1px solid var(--cs-rojo); color: #ff6b6b;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($mensajeError) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Tarjetas Métricas / Accesos Rápidos -->
        <div class="row g-3 mb-4">
            <div class="col-xl col-md-4 col-sm-6">
                <a href="index.php?action=admin-peliculas" class="text-decoration-none">
                    <div class="sala-card h-100 p-3" style="max-width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small text-uppercase fw-semibold">Películas</span>
                                <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= $totalPeliculas ?></h3>
                            </div>
                            <div class="rounded-circle p-3"
                                style="background-color: rgba(245, 185, 33, 0.15); color: var(--cs-dorado);">
                                <i class="bi bi-film fs-2"></i>
                            </div>
                        </div>
                        <span class="small text-white-50 mt-3 d-inline-block">Gestionar títulos y afiches</span>
                    </div>
                </a>
            </div>

            <div class="col-xl col-md-4 col-sm-6">
                <a href="index.php?action=admin-salas" class="text-decoration-none">
                    <div class="sala-card h-100 p-3" style="max-width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small text-uppercase fw-semibold">Salas Físicas</span>
                                <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= $totalSalas ?></h3>
                            </div>
                            <div class="rounded-circle p-3"
                                style="background-color: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                                <i class="bi bi-door-open fs-2"></i>
                            </div>
                        </div>
                        <span class="small text-white-50 mt-3 d-inline-block">Capacidad y configuración</span>
                    </div>
                </a>
            </div>

            <div class="col-xl col-md-4 col-sm-6">
                <a href="index.php?action=admin-funciones" class="text-decoration-none">
                    <div class="sala-card h-100 p-3" style="max-width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small text-uppercase fw-semibold">Funciones</span>
                                <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= $totalFunciones ?></h3>
                            </div>
                            <div class="rounded-circle p-3"
                                style="background-color: rgba(217, 30, 24, 0.15); color: var(--cs-rojo);">
                                <i class="bi bi-calendar2-week fs-2"></i>
                            </div>
                        </div>
                        <span class="small text-white-50 mt-3 d-inline-block">Horarios y cartelera</span>
                    </div>
                </a>
            </div>

            <div class="col-xl col-md-6 col-sm-6">
                <a href="index.php?action=admin-productos" class="text-decoration-none">
                    <div class="sala-card h-100 p-3" style="max-width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small text-uppercase fw-semibold">Dulcería</span>
                                <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= $totalProductos ?></h3>
                            </div>
                            <div class="rounded-circle p-3"
                                style="background-color: rgba(245, 185, 33, 0.15); color: #f5b921;">
                                <i class="bi bi-cup-straw fs-2"></i>
                            </div>
                        </div>
                        <span class="small text-white-50 mt-3 d-inline-block">Combos, precios y stock</span>
                    </div>
                </a>
            </div>

            <div class="col-xl col-md-6 col-sm-6">
                <a href="index.php?action=admin-usuarios" class="text-decoration-none">
                    <div class="sala-card h-100 p-3" style="max-width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small text-uppercase fw-semibold">Usuarios</span>
                                <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= $totalUsuarios ?></h3>
                            </div>
                            <div class="rounded-circle p-3"
                                style="background-color: rgba(16, 185, 129, 0.15); color: #34d399;">
                                <i class="bi bi-people fs-2"></i>
                            </div>
                        </div>
                        <span class="small text-white-50 mt-3 d-inline-block">Cajeros y contraseñas</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Acceso Rápido a Cortes de Caja -->
        <div class="sala-card p-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3"
            style="max-width: 100%; border-left: 4px solid var(--cs-dorado);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3"
                    style="background-color: rgba(245, 185, 33, 0.15); color: var(--cs-dorado);">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <h5 class="text-white mb-1 fw-bold">Auditoría y Arqueos de Caja</h5>
                    <p class="text-white-50 small mb-0">Consulte los cierres de turno, fondos iniciales, recaudación y
                        diferencias de efectivo de cada cajero.</p>
                </div>
            </div>
            <a href="index.php?action=admin-cortes" class="btn btn-warning fw-semibold px-4">
                <i class="bi bi-clipboard2-data me-1"></i> Ver Cortes de Caja
            </a>
        </div>

        <!-- Acceso Rápido a Reportes Imprimibles -->
        <div class="sala-card p-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3"
            style="max-width: 100%; border-left: 4px solid var(--cs-rojo);">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3"
                    style="background-color: rgba(217, 30, 24, 0.15); color: var(--cs-rojo);">
                    <i class="bi bi-file-earmark-bar-graph fs-3"></i>
                </div>
                <div>
                    <h5 class="text-white mb-1 fw-bold">Informes y Reportes Imprimibles</h5>
                    <p class="text-white-50 small mb-0">Genere e imprima reportes oficiales de ventas consolidadas, ranking de dulcería, existencias de inventario y arqueos de turno.</p>
                </div>
            </div>
            <a href="index.php?action=admin-reportes" class="btn btn-danger fw-semibold px-4">
                <i class="bi bi-printer me-1"></i> Generar Reportes
            </a>
        </div>

        <!-- Próximas Funciones Registradas -->
        <div class="sala-card p-4" style="max-width: 100%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 text-white"
                    style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; font-size: 1.5rem;">
                    <i class="bi bi-clock-history me-2 text-danger"></i> ÚLTIMAS FUNCIONES PROGRAMADAS
                </h4>
                <a href="index.php?action=admin-funciones" class="btn btn-sm btn-outline-danger">Ver todas</a>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--cs-gris-borde); color: var(--cs-blanco-suave);">
                            <th>Película</th>
                            <th>Sala</th>
                            <th>Fecha y Hora</th>
                            <th>Formato</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($funciones)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-white-50 py-3">No hay funciones registradas.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach (array_slice($funciones, 0, 5) as $f): ?>
                        <tr>
                            <td class="fw-semibold text-white"><?= htmlspecialchars($f['pelicula_titulo']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($f['nombre_sala']) ?></span></td>
                            <td><?= htmlspecialchars($f['fecha_formato']) ?> ·
                                <?= htmlspecialchars($f['hora_formato']) ?></td>
                            <td><span class="badge text-bg-warning"><?= htmlspecialchars($f['formato']) ?></span></td>
                            <td class="fw-bold text-success">$<?= number_format((float)$f['precio_boleto'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>