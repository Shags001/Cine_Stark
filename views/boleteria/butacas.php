<?php
/** @var array $detalleFuncion */
/** @var array $filas */
/** @var array $columnas */
/** @var array $butacasOcupadas */
/** @var string $nombreCajero */
/** @var string $nombreRol */
$detalleFuncion = $detalleFuncion ?? [];
$filas = $filas ?? [];
$columnas = $columnas ?? [];
$butacasOcupadas = $butacasOcupadas ?? [];
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRol = $nombreRol ?? 'Usuario';
$turnoActivo = $turnoActivo ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletería — Selección de Butacas | Cine Stark</title>

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
        </div>

        <div class="pos-stepper" aria-label="Pasos de venta de boletería">
            <a href="index.php?action=boleteria" class="pos-pill step-pill" style="text-decoration:none;">Función</a>
            <span class="pos-pill step-pill active">Butaca</span>
            <span class="pos-pill step-pill">Cobrar</span>
        </div>
    </nav>

    <!-- Contenido Principal: Paso 2 Mapa de Butacas -->
    <main class="pos-content">
        <!-- Detalle de la función seleccionada -->
        <h2 class="subtitulo-funcion mb-4">
            <?= htmlspecialchars($detalleFuncion['nombre_sala']) ?> ·
            <?= htmlspecialchars($detalleFuncion['pelicula']) ?> ·
            <?= htmlspecialchars($detalleFuncion['formato']) ?> ·
            <?= htmlspecialchars($detalleFuncion['horario_formato']) ?>
        </h2>

        <div class="butacas-layout">
            <!-- Columna Izquierda: Mapa de Butacas -->
            <section class="sala-interactive-panel">
                <div class="pantalla-wrapper">
                    <div class="pantalla-bar">PANTALLA</div>
                </div>

                <div class="matriz-butacas">
                    <?php foreach ($filas as $fila): ?>
                        <div class="fila-asientos">
                            <span class="fila-indicador"><?= $fila ?></span>
                            <div class="asientos-row">
                                <?php foreach ($columnas as $col): ?>
                                    <?php
                                        $codigo = $fila . $col;
                                        $esOcupada = in_array($codigo, $butacasOcupadas);
                                    ?>
                                    <?php if ($esOcupada): ?>
                                        <button type="button" class="asiento-btn ocupado" disabled title="Butaca <?= $codigo ?> (Ocupada)">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button"
                                                class="asiento-btn libre"
                                                data-codigo="<?= $codigo ?>"
                                                title="Butaca <?= $codigo ?> (Libre)">
                                            <i class="bi bi-check-lg icon-check"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Columna Derecha: Resumen de Selección -->
            <aside class="resumen-panel">
                <div class="resumen-card">
                    <h3 class="resumen-titulo">RESUMEN DE SELECCIÓN</h3>

                    <div class="resumen-datos">
                        <div class="resumen-item">
                            <span class="resumen-label">Sala</span>
                            <span class="resumen-valor"><?= htmlspecialchars($detalleFuncion['nombre_sala']) ?> (<?= htmlspecialchars($detalleFuncion['formato']) ?>)</span>
                        </div>
                        <div class="resumen-item">
                            <span class="resumen-label">Horario</span>
                            <span class="resumen-valor"><?= htmlspecialchars($detalleFuncion['horario_formato']) ?></span>
                        </div>
                        <div class="resumen-item">
                            <span class="resumen-label">Butacas</span>
                            <span class="resumen-valor text-dorado fw-bold" id="txtButacas">-</span>
                        </div>
                        <div class="resumen-item">
                            <span class="resumen-label">Cantidad</span>
                            <span class="resumen-valor" id="txtCantidad">0 boletos</span>
                        </div>
                    </div>

                    <!-- Leyenda de colores -->
                    <div class="leyenda-box">
                        <div class="leyenda-item">
                            <span class="leyenda-cuadro libre"></span>
                            <span>Libre</span>
                        </div>
                        <div class="leyenda-item">
                            <span class="leyenda-cuadro seleccionada"><i class="bi bi-check"></i></span>
                            <span>Seleccionada</span>
                        </div>
                        <div class="leyenda-item">
                            <span class="leyenda-cuadro ocupada"><i class="bi bi-x"></i></span>
                            <span>Ocupada</span>
                        </div>
                    </div>

                    <p class="leyenda-nota">
                        Las butacas doradas quedan bloqueadas para los demás cajeros.
                    </p>

                    <form id="formContinuar" action="index.php?action=paso3" method="POST" class="resumen-acciones">
                        <input type="hidden" name="id_funcion" value="<?= htmlspecialchars($detalleFuncion['id_funcion']) ?>">
                        <input type="hidden" name="butacas" id="inputButacas" value="">

                        <a href="index.php?action=boleteria" class="btn-resumen-cancelar">Cancelar</a>
                        <button type="submit" id="btnContinuar" class="btn-resumen-continuar" disabled>Continuar</button>
                    </form>
                </div>
            </aside>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const butacasSeleccionadas = new Set();
        const txtButacas = document.getElementById('txtButacas');
        const txtCantidad = document.getElementById('txtCantidad');
        const inputButacas = document.getElementById('inputButacas');
        const btnContinuar = document.getElementById('btnContinuar');

        document.querySelectorAll('.asiento-btn.libre').forEach(btn => {
            btn.addEventListener('click', function() {
                const codigo = this.getAttribute('data-codigo');

                if (butacasSeleccionadas.has(codigo)) {
                    butacasSeleccionadas.delete(codigo);
                    this.classList.remove('seleccionada');
                } else {
                    butacasSeleccionadas.add(codigo);
                    this.classList.add('seleccionada');
                }

                actualizarResumen();
            });
        });

        function actualizarResumen() {
            const arr = Array.from(butacasSeleccionadas).sort();
            const total = arr.length;

            if (total === 0) {
                txtButacas.textContent = '-';
                txtCantidad.textContent = '0 boletos';
                inputButacas.value = '';
                btnContinuar.disabled = true;
            } else {
                txtButacas.textContent = arr.join(', ');
                txtCantidad.textContent = total + (total === 1 ? ' boleto' : ' boletos');
                inputButacas.value = arr.join(',');
                btnContinuar.disabled = false;
            }
        }
    </script>
</body>
</html>
