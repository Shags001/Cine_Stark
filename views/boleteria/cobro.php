<?php
/** @var array $detalleFuncion */
/** @var array $butacas */
/** @var int $idFuncion */
/** @var float $precioGeneral */
/** @var float $precioNinos */
/** @var float $precioPromo */
/** @var string $nombreCajero */
/** @var string $nombreRol */
$detalleFuncion = $detalleFuncion ?? [];
$butacas = $butacas ?? [];
$idFuncion = $idFuncion ?? 0;
$precioGeneral = $precioGeneral ?? 0.0;
$precioNinos = $precioNinos ?? 0.0;
$precioPromo = $precioPromo ?? 0.0;
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRol = $nombreRol ?? 'Usuario';
$turnoActivo = $turnoActivo ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletería — Cobro y Detalle de Venta | Cine Stark</title>

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
            <a href="index.php?action=paso2&id_funcion=<?= urlencode($idFuncion) ?>" class="pos-pill step-pill" style="text-decoration:none;">Butaca</a>
            <span class="pos-pill step-pill active">Cobrar</span>
        </div>
    </nav>

    <!-- Contenido Principal: Paso 3 Cobro -->
    <main class="pos-content">
        <div class="cobro-layout">

            <!-- Columna Izquierda: Detalle de la Venta -->
            <section class="detalle-venta-card">
                <h2 class="cobro-card-title">DETALLE DE LA VENTA</h2>

                <div class="detalle-filas">
                    <div class="detalle-row">
                        <span class="detalle-label">Película</span>
                        <span class="detalle-valor fw-bold"><?= htmlspecialchars($detalleFuncion['pelicula']) ?></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label">Sala / formato</span>
                        <span class="detalle-valor"><?= htmlspecialchars($detalleFuncion['nombre_sala']) ?> · <?= htmlspecialchars($detalleFuncion['formato']) ?></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label">Horario</span>
                        <span class="detalle-valor"><?= htmlspecialchars($detalleFuncion['horario_formato']) ?></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label">Butacas</span>
                        <span class="detalle-valor text-dorado fw-bold"><?= htmlspecialchars(implode(', ', $butacas)) ?></span>
                    </div>

                    <!-- Selector de Tarifa -->
                    <div class="tarifa-seccion">
                        <label class="tarifa-label">Tarifa</label>
                        <div class="tarifa-buttons">
                            <button type="button" class="btn-tarifa active" data-tarifa="General" data-precio="<?= $precioGeneral ?>">
                                General
                            </button>
                            <button type="button" class="btn-tarifa" data-tarifa="Niños" data-precio="<?= $precioNinos ?>">
                                Niños
                            </button>
                            <button type="button" class="btn-tarifa" data-tarifa="Promoción" data-precio="<?= $precioPromo ?>">
                                Promoción
                            </button>
                        </div>
                    </div>

                    <hr class="detalle-divider">

                    <div class="detalle-subtotal-row">
                        <span id="txtDesglose"><?= count($butacas) ?> boletos x $<?= number_format($precioGeneral, 2) ?></span>
                        <span class="subtotal-monto" id="txtSubtotal">$<?= number_format(count($butacas) * $precioGeneral, 2) ?></span>
                    </div>
                </div>
            </section>

            <!-- Columna Derecha: Panel de Cobro -->
            <section class="cobro-panel-card">
                <form id="formProcesarVenta" action="index.php?action=procesar-venta" method="POST">
                    <input type="hidden" name="id_funcion" value="<?= htmlspecialchars($idFuncion) ?>">
                    <input type="hidden" name="butacas" value="<?= htmlspecialchars(implode(',', $butacas)) ?>">
                    <input type="hidden" name="tarifa" id="inputTarifa" value="General">
                    <input type="hidden" name="metodo_pago" id="inputMetodoPago" value="Efectivo">
                    <input type="hidden" name="total" id="inputTotal" value="<?= count($butacas) * $precioGeneral ?>">
                    <input type="hidden" name="efectivo_recibido" id="inputEfectivoRecibido" value="">
                    <input type="hidden" name="cambio" id="inputCambio" value="0.00">

                    <!-- Total a Cobrar -->
                    <div class="total-bloque">
                        <span class="total-label-dorado">TOTAL A COBRAR</span>
                        <div class="total-monto" id="displayTotal">$ <?= number_format(count($butacas) * $precioGeneral, 2) ?></div>
                    </div>

                    <!-- Métodos de Pago Toggle -->
                    <div class="metodos-pago-buttons mb-4">
                        <button type="button" class="btn-metodo active" data-metodo="Efectivo">
                            Efectivo
                        </button>
                        <button type="button" class="btn-metodo" data-metodo="Tarjeta">
                            Tarjeta
                        </button>
                    </div>

                    <!-- Sección Efectivo y Cambio -->
                    <div id="seccionEfectivo" class="seccion-efectivo-bloque">
                        <div class="mb-3">
                            <label for="campoEfectivo" class="form-label-stark text-start">Efectivo recibido</label>
                            <div class="input-moneda-wrapper">
                                <span class="input-moneda-simbolo">$</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control input-efectivo"
                                    id="campoEfectivo"
                                    placeholder="0.00"
                                    autocomplete="off"
                                    autofocus
                                >
                            </div>
                        </div>

                        <div class="cambio-bloque">
                            <span class="cambio-label">Cambio</span>
                            <div class="cambio-monto" id="displayCambio">$ 0.00</div>
                        </div>

                        <div id="alertaFaltante" class="alerta-faltante d-none">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                            Faltante: <strong id="montoFaltante">$ 0.00</strong>
                        </div>
                    </div>

                    <!-- Botón Gigante de Cobro -->
                    <button type="submit" id="btnCobrar" class="btn-cobrar-stark" disabled>
                        COBRAR E IMPRIMIR TICKET
                    </button>
                </form>
            </section>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const cantidadBoletos = <?= count($butacas) ?>;
        let precioUnitario = <?= $precioGeneral ?>;
        let metodoPagoActual = 'Efectivo';
        let totalActual = cantidadBoletos * precioUnitario;

        const displayTotal = document.getElementById('displayTotal');
        const displayCambio = document.getElementById('displayCambio');
        const campoEfectivo = document.getElementById('campoEfectivo');
        const alertaFaltante = document.getElementById('alertaFaltante');
        const montoFaltante = document.getElementById('montoFaltante');
        const btnCobrar = document.getElementById('btnCobrar');

        const inputTarifa = document.getElementById('inputTarifa');
        const inputMetodoPago = document.getElementById('inputMetodoPago');
        const inputTotal = document.getElementById('inputTotal');
        const inputEfectivoRecibido = document.getElementById('inputEfectivoRecibido');
        const inputCambio = document.getElementById('inputCambio');

        const txtDesglose = document.getElementById('txtDesglose');
        const txtSubtotal = document.getElementById('txtSubtotal');
        const seccionEfectivo = document.getElementById('seccionEfectivo');

        // Toggle de Tarifa
        document.querySelectorAll('.btn-tarifa').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-tarifa').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const tarifaNombre = this.getAttribute('data-tarifa');
                precioUnitario = parseFloat(this.getAttribute('data-precio'));
                inputTarifa.value = tarifaNombre;

                recalcularTotales();
            });
        });

        // Toggle de Método de Pago
        document.querySelectorAll('.btn-metodo').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-metodo').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                metodoPagoActual = this.getAttribute('data-metodo');
                inputMetodoPago.value = metodoPagoActual;

                if (metodoPagoActual === 'Tarjeta') {
                    seccionEfectivo.style.display = 'none';
                    btnCobrar.disabled = false;
                    alertaFaltante.classList.add('d-none');
                    inputEfectivoRecibido.value = totalActual.toFixed(2);
                    inputCambio.value = '0.00';
                } else {
                    seccionEfectivo.style.display = 'block';
                    evaluarEfectivo();
                }
            });
        });

        // Evento de ingreso de efectivo
        campoEfectivo.addEventListener('input', evaluarEfectivo);

        function recalcularTotales() {
            totalActual = cantidadBoletos * precioUnitario;
            displayTotal.textContent = '$ ' + totalActual.toFixed(2);
            inputTotal.value = totalActual.toFixed(2);

            txtDesglose.textContent = cantidadBoletos + ' boletos x $' + precioUnitario.toFixed(2);
            txtSubtotal.textContent = '$' + totalActual.toFixed(2);

            if (metodoPagoActual === 'Efectivo') {
                evaluarEfectivo();
            } else {
                inputEfectivoRecibido.value = totalActual.toFixed(2);
                inputCambio.value = '0.00';
            }
        }

        function evaluarEfectivo() {
            const val = parseFloat(campoEfectivo.value);

            if (isNaN(val) || val <= 0) {
                displayCambio.textContent = '$ 0.00';
                displayCambio.classList.remove('texto-verde');
                btnCobrar.disabled = true;
                alertaFaltante.classList.add('d-none');
                inputEfectivoRecibido.value = '';
                inputCambio.value = '0.00';
                return;
            }

            inputEfectivoRecibido.value = val.toFixed(2);

            if (val >= totalActual) {
                const cambio = val - totalActual;
                displayCambio.textContent = '$ ' + cambio.toFixed(2);
                displayCambio.classList.add('texto-verde');
                btnCobrar.disabled = false;
                alertaFaltante.classList.add('d-none');
                inputCambio.value = cambio.toFixed(2);
            } else {
                const faltante = totalActual - val;
                displayCambio.textContent = '$ 0.00';
                displayCambio.classList.remove('texto-verde');
                montoFaltante.textContent = '$ ' + faltante.toFixed(2);
                alertaFaltante.classList.remove('d-none');
                btnCobrar.disabled = true;
                inputCambio.value = '0.00';
            }
        }
    </script>
</body>
</html>
