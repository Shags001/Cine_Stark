<?php
/** @var array $turnoAbierto */
/** @var float $montoInicial */
/** @var float $totalVentas */
/** @var float $ventasEfectivo */
/** @var float $ventasTarjeta */
/** @var float $totalBoletos */
/** @var float $totalDulceria */
/** @var float $esperadoEnGaveta */
/** @var int $cantidadBoletos */
/** @var int $cantidadDulceria */
/** @var array $transacciones */
/** @var string $nombreCajero */
/** @var string $nombreRol */
$turnoAbierto = $turnoAbierto ?? [];
$montoInicial = $montoInicial ?? 0.0;
$totalVentas = $totalVentas ?? 0.0;
$ventasEfectivo = $ventasEfectivo ?? 0.0;
$ventasTarjeta = $ventasTarjeta ?? 0.0;
$totalBoletos = $totalBoletos ?? 0.0;
$totalDulceria = $totalDulceria ?? 0.0;
$esperadoEnGaveta = $esperadoEnGaveta ?? 0.0;
$cantidadBoletos = $cantidadBoletos ?? 0;
$cantidadDulceria = $cantidadDulceria ?? 0;
$transacciones = $transacciones ?? [];
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRol = $nombreRol ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre y Arqueo de Caja — Cine Stark</title>

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
            <span><?= htmlspecialchars($nombreRol) ?>: <strong class="pos-user-name"><?= htmlspecialchars($nombreCajero) ?></strong> · <span class="pos-status-badge">Turno en curso</span></span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Barra de Navegación -->
    <nav class="pos-toolbar" aria-label="Navegación de módulos">
        <div class="pos-nav-tabs">
            <a href="index.php?action=boleteria" class="pos-pill">Boletería</a>
            <a href="index.php?action=dulceria" class="pos-pill">Dulcería</a>
            <a href="index.php?action=cierre-caja" class="pos-pill active">Cierre de caja</a>
        </div>
    </nav>

    <!-- Contenido Principal: Arqueo y Cierre -->
    <main class="pos-content">
        <h2 class="section-headline mb-4">CONCILIACIÓN Y ARQUEO DE TURNO</h2>

        <div class="cobro-layout mb-4">

            <!-- Columna Izquierda: Resumen del Turno -->
            <section class="detalle-venta-card">
                <h3 class="cobro-card-title">RESUMEN DEL TURNO</h3>

                <div class="detalle-filas">
                    <div class="detalle-row">
                        <span class="detalle-label">Inicio de turno</span>
                        <span class="detalle-valor"><?= date('d/m/Y h:i A', strtotime($turnoAbierto['fecha_inicio'])) ?></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label">Fondo inicial de gaveta</span>
                        <span class="detalle-valor fw-bold">$ <?= number_format($montoInicial, 2) ?></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label"><i class="bi bi-ticket-perforated me-1 text-danger"></i> Ventas Boletería</span>
                        <span class="detalle-valor text-white fw-semibold">$ <?= number_format($totalBoletos, 2) ?> <small class="text-white-50">(<?= $cantidadBoletos ?> boletos)</small></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label"><i class="bi bi-cup-straw me-1 text-warning"></i> Ventas Dulcería</span>
                        <span class="detalle-valor text-white fw-semibold">$ <?= number_format($totalDulceria, 2) ?> <small class="text-white-50">(<?= $cantidadDulceria ?> productos)</small></span>
                    </div>

                    <hr class="detalle-divider">

                    <div class="detalle-row">
                        <span class="detalle-label">Cobros en Efectivo</span>
                        <span class="detalle-valor text-white fw-semibold">$ <?= number_format($ventasEfectivo, 2) ?></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label">Cobros con Tarjeta (POS)</span>
                        <span class="detalle-valor text-white fw-semibold">$ <?= number_format($ventasTarjeta, 2) ?></span>
                    </div>

                    <div class="detalle-row">
                        <span class="detalle-label fw-bold">Total Recaudado</span>
                        <span class="detalle-valor text-dorado fw-bold fs-5">$ <?= number_format($totalVentas, 2) ?></span>
                    </div>

                    <hr class="detalle-divider">

                    <div class="p-3 rounded" style="background-color: rgba(245, 185, 33, 0.08); border: 1px solid rgba(245, 185, 33, 0.25);">
                        <span class="d-block small text-dorado fw-bold mb-1">EFECTIVO ESPERADO EN GAVETA:</span>
                        <div class="fs-3 fw-bold text-white">$ <?= number_format($esperadoEnGaveta, 2) ?></div>
                        <small class="text-white-50">(Fondo inicial $<?= number_format($montoInicial, 2) ?> + Ventas efectivo $<?= number_format($ventasEfectivo, 2) ?>)</small>
                    </div>
                </div>
            </section>

            <!-- Columna Derecha: Arqueo Físico y Confirmación -->
            <section class="cobro-panel-card">
                <h3 class="cobro-card-title">CONTEO FÍSICO (ARQUEO)</h3>

                <form id="formCierreCaja" action="index.php?action=procesar-cierre-caja" method="POST">
                    <input type="hidden" name="id_corte" value="<?= htmlspecialchars($turnoAbierto['id_corte']) ?>">
                    <input type="hidden" name="total_ventas" value="<?= $totalVentas ?>">
                    <input type="hidden" name="esperado" id="inputEsperado" value="<?= $esperadoEnGaveta ?>">
                    <input type="hidden" name="total_declarado" id="inputDeclarado" value="0.00">
                    <input type="hidden" name="diferencia" id="inputDiferencia" value="0.00">

                    <div class="mb-4 text-start">
                        <label for="campoContado" class="form-label-stark">Efectivo físico contado en gaveta</label>
                        <div class="input-moneda-wrapper">
                            <span class="input-moneda-simbolo">$</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control input-efectivo"
                                id="campoContado"
                                placeholder="0.00"
                                required
                                autofocus
                            >
                        </div>
                        <small class="text-white-50 mt-1 d-block">Ingrese el dinero total contado en la caja.</small>
                    </div>

                    <!-- Indicador de Diferencia -->
                    <div id="resultadoArqueo" class="p-3 rounded mb-4 text-center" style="background-color: #1a1a1e; border: 1px solid var(--cs-gris-borde);">
                        <span class="small text-white-50 d-block mb-1">ESTADO DEL ARQUEO:</span>
                        <div id="textoDiferencia" class="fs-4 fw-bold text-white-50">Ingrese el monto contado</div>
                    </div>

                    <button type="submit" id="btnConfirmarCierre" class="btn-cobrar-stark" disabled>
                        <i class="bi bi-lock-fill me-1"></i> CONFIRMAR CIERRE DE CAJA
                    </button>
                </form>
            </section>

        </div>

        <!-- Auditoría de Transacciones del Turno -->
        <section class="detalle-venta-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="cobro-card-title mb-0" style="font-size: 1.5rem; border-bottom: none; padding-bottom: 0;">
                    <i class="bi bi-list-check me-2"></i> AUDITORÍA DE TRANSACCIONES DEL TURNO
                </h3>
                <span class="badge bg-secondary"><?= count($transacciones) ?> transacciones</span>
            </div>

            <?php if (empty($transacciones)): ?>
                <p class="text-white-50 mb-0 small">No se registraron ventas durante este turno.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0" style="background-color: transparent;">
                        <thead>
                            <tr style="border-color: #33333d;">
                                <th># Ticket</th>
                                <th>Módulo</th>
                                <th>Hora</th>
                                <th>Detalle / Concepto</th>
                                <th>Artículos / Butacas</th>
                                <th>Método</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transacciones as $t): ?>
                                <?php $esDulceria = (($t['tipo_venta'] ?? '') === 'dulceria'); ?>
                                <tr style="border-color: #2b2b34;">
                                    <td><strong class="text-dorado">#<?= ($esDulceria ? 'D-' : 'B-') . str_pad($t['id_venta'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                    <td>
                                        <?php if ($esDulceria): ?>
                                            <span class="badge" style="background-color: rgba(245, 185, 33, 0.2); color: #f5b921; border: 1px solid rgba(245, 185, 33, 0.4);"><i class="bi bi-cup-straw me-1"></i>Dulcería</span>
                                        <?php else: ?>
                                            <span class="badge" style="background-color: rgba(217, 30, 24, 0.2); color: #f87171; border: 1px solid rgba(217, 30, 24, 0.4);"><i class="bi bi-ticket-perforated me-1"></i>Boletería</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('h:i A', strtotime($t['fecha_hora'])) ?></td>
                                    <td>
                                        <?php if ($esDulceria): ?>
                                            <span class="text-white fw-semibold">Consumo Dulcería & Snacks</span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($t['pelicula'] ?? 'N/D') ?> <small class="text-white-50">(<?= htmlspecialchars($t['nombre_sala'] ?? '') ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($esDulceria): ?>
                                            <span class="small text-white-50" style="max-width: 250px; display: inline-block;"><?= htmlspecialchars($t['productos_dulceria'] ?? 'Varios') ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-dark border text-light"><?= htmlspecialchars($t['butacas'] ?? '-') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $t['metodo_pago'] === 'Efectivo' ? 'bg-success' : 'bg-primary' ?>">
                                            <?= htmlspecialchars($t['metodo_pago']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-white">$ <?= number_format($t['total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const esperado = <?= $esperadoEnGaveta ?>;
        const campoContado = document.getElementById('campoContado');
        const inputDeclarado = document.getElementById('inputDeclarado');
        const inputDiferencia = document.getElementById('inputDiferencia');
        const textoDiferencia = document.getElementById('textoDiferencia');
        const resultadoArqueo = document.getElementById('resultadoArqueo');
        const btnConfirmarCierre = document.getElementById('btnConfirmarCierre');

        campoContado.addEventListener('input', function() {
            const contado = parseFloat(this.value);

            if (isNaN(contado) || contado < 0) {
                textoDiferencia.innerHTML = 'Ingrese el monto contado';
                textoDiferencia.className = 'fs-4 fw-bold text-white-50';
                resultadoArqueo.style.borderColor = 'var(--cs-gris-borde)';
                btnConfirmarCierre.disabled = true;
                return;
            }

            const dif = contado - esperado;
            inputDeclarado.value = contado.toFixed(2);
            inputDiferencia.value = dif.toFixed(2);
            btnConfirmarCierre.disabled = false;

            if (Math.abs(dif) < 0.01) {
                textoDiferencia.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Cuadre Exacto ($ 0.00)';
                textoDiferencia.className = 'fs-4 fw-bold text-success';
                resultadoArqueo.style.borderColor = '#10b981';
            } else if (dif < 0) {
                const faltante = Math.abs(dif);
                textoDiferencia.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Faltante: -$ ' + faltante.toFixed(2);
                textoDiferencia.className = 'fs-4 fw-bold text-danger';
                resultadoArqueo.style.borderColor = '#FF5A5F';
            } else {
                textoDiferencia.innerHTML = '<i class="bi bi-plus-circle-fill text-warning me-1"></i> Sobrante: +$ ' + dif.toFixed(2);
                textoDiferencia.className = 'fs-4 fw-bold text-warning';
                resultadoArqueo.style.borderColor = 'var(--cs-dorado)';
            }
        });
    </script>
</body>
</html>
