<?php
/** @var array $salasConFunciones */
/** @var string $nombreCajero */
/** @var string $nombreRol */
$salasConFunciones = $salasConFunciones ?? [];
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRol = $nombreRol ?? 'Usuario';
$turnoActivo = $turnoActivo ?? [];
$fechaSeleccionada = $fechaSeleccionada ?? date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletería — Selección de Función | Cine Stark</title>

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
            <h1 class="pos-title">BOLETERÍA</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRol) ?>: <strong class="pos-user-name"><?= htmlspecialchars($nombreCajero) ?></strong> · 
                <?php if (!empty($turnoActivo['id_corte'])): ?>
                    <span class="pos-status-badge">Turno #<?= (int)$turnoActivo['id_corte'] ?> abierto</span>
                <?php else: ?>
                    <span class="pos-status-badge" style="background-color: rgba(245, 185, 33, 0.15); color: #f5b921; border-color: rgba(245, 185, 33, 0.4);">Sin turno activo</span>
                <?php endif; ?>
            </span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Barra de Navegación y Stepper de Pasos -->
    <nav class="pos-toolbar" aria-label="Navegación de módulos y etapas">
        <div class="pos-nav-tabs">
            <a href="index.php?action=boleteria" class="pos-pill active">Boletería</a>
            <a href="index.php?action=dulceria" class="pos-pill">Dulcería</a>
            <a href="index.php?action=cierre-caja" class="pos-pill">Cierre de caja</a>
            <?php if (isset($_SESSION['id_rol']) && ((int)$_SESSION['id_rol'] === 1 || stripos($_SESSION['nombre_rol'] ?? '', 'admin') !== false)): ?>
                <a href="index.php?action=admin-dashboard" class="pos-pill" style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-gear-fill me-1"></i> Administración</a>
            <?php endif; ?>
        </div>

        <div class="pos-stepper" aria-label="Pasos de venta de boletería">
            <span class="pos-pill step-pill active">Función</span>
            <span class="pos-pill step-pill">Butaca</span>
            <span class="pos-pill step-pill">Cobrar</span>
        </div>
    </nav>

    <!-- Contenido Principal: Paso 1 Selección de Función -->
    <main class="pos-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="section-headline mb-0">SELECCIONE LA FUNCIÓN DEL DÍA</h2>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 px-3 py-1 rounded" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde);">
                    <i class="bi bi-calendar3 text-white-50"></i>
                    <input
                        type="date"
                        id="filtroFecha"
                        value="<?= htmlspecialchars($fechaSeleccionada) ?>"
                        class="form-control form-control-sm border-0 bg-transparent text-white px-1"
                        style="outline: none; box-shadow: none;"
                        title="Seleccionar fecha de funciones"
                        onchange="window.location.href='index.php?action=boleteria&fecha=' + this.value"
                    >
                </div>

                <div class="pos-search-wrapper">
                    <i class="bi bi-search pos-search-icon"></i>
                    <input
                        type="text"
                        id="inputBuscarFuncion"
                        class="pos-search-input"
                        placeholder="Buscar película, sala o formato..."
                        autocomplete="off"
                    >
                    <button type="button" id="btnLimpiarBusqueda" class="pos-search-clear d-none" aria-label="Limpiar búsqueda">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="sinResultadosBusqueda" class="alert alert-dark text-center py-4 d-none" style="background-color: var(--cs-gris-carbon); border-color: var(--cs-gris-borde);">
            <i class="bi bi-search fs-3 text-white-50 d-block mb-2"></i>
            <h5 class="text-white mb-1">No se encontraron funciones</h5>
            <p class="text-white-50 small mb-0">Ninguna función o película coincide con su criterio de búsqueda.</p>
        </div>

        <?php if (empty($salasConFunciones)): ?>
            <div class="alert alert-warning text-center my-4 py-4" style="background-color: #26262B; border-color: var(--cs-dorado); color: var(--cs-dorado);">
                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                <h5>No hay funciones programadas para <?= htmlspecialchars(date('d/m/Y', strtotime($fechaSeleccionada))) ?></h5>
                <p class="mb-0 small text-white-50">Comuníquese con el administrador para registrar horarios en cartelera.</p>
            </div>
        <?php else: ?>
            <div class="salas-grid">
                <?php foreach ($salasConFunciones as $sala): ?>
                    <article class="sala-card">
                        <header class="sala-header">
                            <?= htmlspecialchars($sala['nombre_sala']) ?> · <?= htmlspecialchars($sala['capacidad']) ?> butacas
                        </header>

                        <div class="sala-afiche" title="<?= htmlspecialchars($sala['pelicula']) ?>">
                            <?php if (!empty($sala['imagen']) && file_exists(__DIR__ . '/../../img/' . $sala['imagen'])): ?>
                                <img src="img/<?= htmlspecialchars($sala['imagen']) ?>" alt="<?= htmlspecialchars($sala['pelicula']) ?>" class="afiche-poster-img">
                            <?php else: ?>
                                <i class="bi bi-film"></i>
                                <span>Afiche</span>
                            <?php endif; ?>
                        </div>

                        <h3 class="movie-title"><?= htmlspecialchars($sala['pelicula']) ?></h3>

                        <span class="format-badge"><?= htmlspecialchars($sala['formato']) ?></span>

                        <div class="horarios-container">
                            <?php foreach ($sala['horarios'] as $h): ?>
                                <a href="index.php?action=paso2&id_funcion=<?= urlencode($h['id_funcion']) ?>"
                                   class="btn-horario"
                                   title="Seleccionar horario <?= htmlspecialchars($h['horario_formato']) ?> ($<?= number_format($h['precio_boleto'], 2) ?>)">
                                    <?= htmlspecialchars($h['horario_formato']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <p class="sala-footer-hint">Toque un horario para ver la sala</p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <footer class="pos-footer-notes">
            <span>Tarifas disponibles: <strong>General</strong> · <strong>Niños</strong> · <strong>Promoción</strong></span>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const inputBuscar = document.getElementById('inputBuscarFuncion');
        const btnLimpiar = document.getElementById('btnLimpiarBusqueda');
        const tarjetas = document.querySelectorAll('.sala-card');
        const sinResultados = document.getElementById('sinResultadosBusqueda');

        if (inputBuscar) {
            inputBuscar.addEventListener('input', function() {
                const termino = this.value.toLowerCase().trim();
                let visibles = 0;

                if (termino.length > 0) {
                    btnLimpiar.classList.remove('d-none');
                } else {
                    btnLimpiar.classList.add('d-none');
                }

                tarjetas.forEach(tarjeta => {
                    const texto = tarjeta.textContent.toLowerCase();
                    if (texto.includes(termino)) {
                        tarjeta.style.display = 'flex';
                        visibles++;
                    } else {
                        tarjeta.style.display = 'none';
                    }
                });

                if (visibles === 0 && tarjetas.length > 0) {
                    sinResultados.classList.remove('d-none');
                } else {
                    sinResultados.classList.add('d-none');
                }
            });

            btnLimpiar.addEventListener('click', function() {
                inputBuscar.value = '';
                inputBuscar.dispatchEvent(new Event('input'));
                inputBuscar.focus();
            });

            inputBuscar.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.dispatchEvent(new Event('input'));
                }
            });
        }
    </script>
</body>
</html>
