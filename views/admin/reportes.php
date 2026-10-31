<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var string $tipo */
/** @var string $fechaInicio */
/** @var string $fechaFin */
/** @var int $idUsuario */
/** @var array $usuarios */
/** @var array $datosReporte */
$nombreAdmin = $nombreAdmin ?? 'Administrador';
$nombreRol   = $nombreRol ?? 'Administrador';
$tipo        = $tipo ?? 'ventas';
$fechaInicio = $fechaInicio ?? date('Y-m-01');
$fechaFin    = $fechaFin ?? date('Y-m-d');
$idUsuario   = $idUsuario ?? 0;
$usuarios    = $usuarios ?? [];
$datosReporte = $datosReporte ?? [];

$titulosReportes = [
    'ventas'     => 'INFORME GENERAL DE VENTAS Y RECAUDACIÓN',
    'dulceria'   => 'INFORME DE RENDIMIENTO Y VENTAS DE DULCERÍA',
    'cortes'     => 'INFORME DE AUDITORÍA Y CORTES DE CAJA',
    'inventario' => 'INFORME DE INVENTARIO VALORIZADO Y EXISTENCIAS'
];

$subtitulosReportes = [
    'ventas'     => 'Consolidado de transacciones de boletería y mostrador de dulcería',
    'dulceria'   => 'Ranking de productos y combos más vendidos con participación de ingresos',
    'cortes'     => 'Consolidación de turnos, arqueos, declaraciones y cuadres de gaveta',
    'inventario' => 'Auditoría física de existencias, productos agotados y valoración total'
];

$tituloActual = $titulosReportes[$tipo] ?? 'INFORME EMPRESARIAL';
$subtituloActual = $subtitulosReportes[$tipo] ?? '';

// Nombre de cajero filtrado si aplica
$nombreCajeroFiltro = 'Todos los usuarios / cajeros';
if ($idUsuario > 0) {
    foreach ($usuarios as $u) {
        if ((int)$u['id_usuario'] === $idUsuario) {
            $nombreCajeroFiltro = $u['nombre'] . ' (@' . $u['usuario'] . ')';
            break;
        }
    }
}

$urlPdf = "index.php?action=admin-reporte-pdf&tipo=" . urlencode($tipo) . "&fecha_inicio=" . urlencode($fechaInicio) . "&fecha_fin=" . urlencode($fechaFin) . ($idUsuario > 0 ? "&id_usuario=" . $idUsuario : "");
$folio = 'CS-' . strtoupper(substr($tipo, 0, 3)) . '-' . date('Ymd') . '-' . rand(1000, 9999);
$selloHash = strtoupper(substr(hash('sha256', $folio . date('Y-m-d') . $nombreAdmin), 0, 24));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloActual) ?> — Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/boleteria.css" rel="stylesheet">
    <link href="public/css/admin-reportes.css" rel="stylesheet">
</head>

<body class="pos-body">

    <!-- Encabezado superior con logotipo, usuario en sesión y enlace para cerrar sesión -->
    <header class="pos-header d-print-none">
        <div class="pos-brand">
            <img src="img/cinestarklogo.png" alt="Cine Stark" class="pos-logo-img">
            <h1 class="pos-title">CENTRO DE REPORTES</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRol) ?>: <strong
                    class="pos-user-name"><?= htmlspecialchars($nombreAdmin) ?></strong></span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Menú de navegación principal del panel de administración -->
    <nav class="pos-toolbar d-print-none" aria-label="Navegación del Administrador">
        <div class="pos-nav-tabs">
            <a href="index.php?action=admin-dashboard" class="pos-pill"><i class="bi bi-speedometer2 me-1"></i>
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
            <a href="index.php?action=admin-reportes" class="pos-pill active"><i
                    class="bi bi-file-earmark-bar-graph me-1"></i> Reportes</a>
            <a href="index.php?action=boleteria" class="pos-pill"
                style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i
                    class="bi bi-ticket-perforated me-1"></i> Boletería</a>
        </div>
    </nav>

    <!-- Contenedor central del módulo de reportes -->
    <main class="pos-content py-3">

        <!-- Selector de pestañas para alternar entre los cuatro formatos de reporte -->
        <div class="d-print-none mb-4">
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?action=admin-reportes&tipo=ventas&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?><?= $idUsuario > 0 ? '&id_usuario=' . $idUsuario : '' ?>"
                    class="reporte-tab-btn <?= $tipo === 'ventas' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow"></i> 1. Ventas Generales
                </a>
                <a href="index.php?action=admin-reportes&tipo=dulceria&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>"
                    class="reporte-tab-btn <?= $tipo === 'dulceria' ? 'active' : '' ?>">
                    <i class="bi bi-cup-straw"></i> 2. Ranking Dulcería
                </a>
                <a href="index.php?action=admin-reportes&tipo=cortes&fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?><?= $idUsuario > 0 ? '&id_usuario=' . $idUsuario : '' ?>"
                    class="reporte-tab-btn <?= $tipo === 'cortes' ? 'active' : '' ?>">
                    <i class="bi bi-cash-stack"></i> 3. Auditoría de Turnos
                </a>
                <a href="index.php?action=admin-reportes&tipo=inventario"
                    class="reporte-tab-btn <?= $tipo === 'inventario' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i> 4. Inventario y Stock
                </a>
            </div>
        </div>

        <!-- Panel de filtros por período, selección de cajero y botón de descarga en PDF -->
        <div class="sala-card p-3 mb-4 d-print-none" style="max-width: 100%;">
            <?php if ($tipo === 'inventario'): ?>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="text-white fw-medium">Auditoría física y valuación de stock de inventario</span>
                    <div class="text-white-50 small">Cálculo de valores y existencias</div>
                </div>
                <div>
                    <a href="<?= $urlPdf ?>" target="_blank" class="btn btn-sm btn-danger fw-semibold px-4"
                        style="height: 36px; min-width: 150px; display: inline-flex; align-items: center; justify-content: center;">
                        Descargar PDF
                    </a>
                </div>
            </div>
            <?php else: ?>
            <form action="index.php" method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="action" value="admin-reportes">
                <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">

                <div class="col-auto">
                    <label class="form-label text-white-50 small mb-1 fw-semibold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($fechaInicio) ?>"
                        style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                </div>

                <div class="col-auto">
                    <label class="form-label text-white-50 small mb-1 fw-semibold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($fechaFin) ?>"
                        style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                </div>

                <?php if (in_array($tipo, ['ventas', 'cortes'], true)): ?>
                <div class="col-auto">
                    <label class="form-label text-white-50 small mb-1 fw-semibold">Filtrar por Cajero</label>
                    <select name="id_usuario" class="form-select form-select-sm"
                        style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                        <option value="0">-- Todos los cajeros --</option>
                        <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"
                            <?= $idUsuario === (int)$u['id_usuario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['usuario']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-outline-warning px-3" style="height: 36px;">
                        Filtrar
                    </button>
                </div>

                <div class="col ms-auto text-end">
                    <a href="<?= $urlPdf ?>" target="_blank" class="btn btn-sm btn-danger fw-semibold px-4"
                        style="height: 36px; min-width: 150px; display: inline-flex; align-items: center; justify-content: center;">
                        Descargar PDF
                    </a>
                </div>
            </form>
            <?php endif; ?>
        </div>


        <article class="reporte-papel" id="areaReporteImprimible">

            <!-- Membrete con datos fiscales de la sociedad, fecha de emisión y usuario responsable -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4"
                style="border-color: rgba(255,255,255,0.15) !important;">
                <div class="d-flex align-items-center gap-3">
                    <img src="img/cinestarklogo.png" alt="Cine Stark" style="max-height: 52px;" class="print-logo-img">
                    <div>
                        <h2 class="mb-0 fw-bold"
                            style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; font-size: 2rem;">CINE
                            STARK S.A. DE C.V.</h2>
                        <div class="small" style="opacity: 0.7;">NIT: 0614-281026-101-3 · IVA: 248910-1 · Complejo
                            Cinematográfico Stark</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-secondary mb-1">REPORTE OFICIAL</span>
                    <div class="small" style="opacity: 0.7;">Emisión: <?= date('d/m/Y h:i A') ?></div>
                    <div class="small" style="opacity: 0.7;">Generado por: <?= htmlspecialchars($nombreAdmin) ?></div>
                </div>
            </div>

            <!-- Título del reporte activo y resumen de parámetros filtrados -->
            <div class="mb-4 text-center">
                <h3 class="fw-bold mb-1" style="letter-spacing: 0.5px;"><?= htmlspecialchars($tituloActual) ?></h3>
                <p class="small mb-2" style="opacity: 0.75;"><?= htmlspecialchars($subtituloActual) ?></p>

                <div class="d-inline-flex flex-wrap justify-content-center gap-3 py-1 px-3 rounded"
                    style="background-color: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.15); font-size: 0.85rem;">
                    <?php if ($tipo !== 'inventario'): ?>
                    <span><strong>Período:</strong> <?= date('d/m/Y', strtotime($fechaInicio)) ?> al
                        <?= date('d/m/Y', strtotime($fechaFin)) ?></span>
                    <?php endif; ?>
                    <?php if (in_array($tipo, ['ventas', 'cortes'], true)): ?>
                    <span><strong>Cajero auditado:</strong> <?= htmlspecialchars($nombreCajeroFiltro) ?></span>
                    <?php endif; ?>
                    <span><strong>Estado:</strong> Consolidado Auditado</span>
                </div>
            </div>

            <!-- Reporte de Ventas Generales: Métrica global, recaudación por canal y desglose de transacciones -->
            <?php if ($tipo === 'ventas'): ?>
            <?php
                    $tot = $datosReporte['totales'] ?? [];
                    $txs = $datosReporte['transacciones'] ?? [];
                ?>
            <!-- Indicadores clave de ventas: ingresos totales, boletos, dulcería y métodos de pago -->
            <div class="row g-2 mb-4">
                <div class="col-md-2 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Total Recaudado</span>
                        <div class="kpi-report-value text-warning">
                            $<?= number_format((float)($tot['total_recaudado'] ?? 0), 2) ?></div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Ventas Boletería</span>
                        <div class="kpi-report-value">$<?= number_format((float)($tot['total_boletos'] ?? 0), 2) ?>
                        </div>
                        <small style="font-size: 0.7rem; opacity: 0.7;"><?= (int)($tot['cantidad_boletos'] ?? 0) ?>
                            boletos</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Ventas Dulcería</span>
                        <div class="kpi-report-value text-dorado">
                            $<?= number_format((float)($tot['total_dulceria'] ?? 0), 2) ?></div>
                        <small style="font-size: 0.7rem; opacity: 0.7;"><?= (int)($tot['cantidad_dulceria'] ?? 0) ?>
                            artículos</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Cobros Efectivo</span>
                        <div class="kpi-report-value text-success">
                            $<?= number_format((float)($tot['total_efectivo'] ?? 0), 2) ?></div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Cobros Tarjeta</span>
                        <div class="kpi-report-value text-info">
                            $<?= number_format((float)($tot['total_tarjeta'] ?? 0), 2) ?></div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Transacciones</span>
                        <div class="kpi-report-value"><?= (int)($tot['total_transacciones'] ?? 0) ?></div>
                    </div>
                </div>
            </div>

            <!-- Tabla detallada de transacciones individuales con detalle de productos o butacas -->
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th># Ticket</th>
                            <th>Módulo</th>
                            <th>Fecha / Hora</th>
                            <th>Cajero</th>
                            <th>Concepto / Detalle</th>
                            <th>Butacas / Ítems</th>
                            <th>Método</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($txs)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4" style="opacity: 0.6;">No se registraron ventas en
                                el período seleccionado.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($txs as $t): ?>
                        <?php $esDulce = (($t['tipo_venta'] ?? '') === 'dulceria'); ?>
                        <tr>
                            <td><strong>#<?= ($esDulce ? 'D-' : 'B-') . str_pad($t['id_venta'], 5, '0', STR_PAD_LEFT) ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $esDulce ? 'bg-warning text-dark' : 'bg-danger' ?>">
                                    <?= $esDulce ? 'Dulcería' : 'Boletería' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y h:i A', strtotime($t['fecha_hora'])) ?></td>
                            <td><?= htmlspecialchars($t['nombre_cajero']) ?></td>
                            <td>
                                <?php if ($esDulce): ?>
                                <span>Consumo Bar / Dulcería</span>
                                <?php else: ?>
                                <?= htmlspecialchars($t['pelicula'] ?? 'N/D') ?> <small
                                    style="opacity:0.7;">(<?= htmlspecialchars($t['nombre_sala'] ?? '') ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($esDulce): ?>
                                <small
                                    style="opacity:0.85;"><?= htmlspecialchars($t['productos_dulceria'] ?? 'Varios') ?></small>
                                <?php else: ?>
                                <small><?= htmlspecialchars($t['butacas'] ?? '-') ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($t['metodo_pago']) ?></td>
                            <td class="text-end fw-bold">$<?= number_format((float)$t['total'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Reporte de Ranking de Dulcería: Desempeño comercial por producto, unidades vendidas y cuota de ingresos -->
            <?php elseif ($tipo === 'dulceria'): ?>
            <?php
                    $ranking = $datosReporte;
                    $granTotalDulceria = array_reduce($ranking, fn($acc, $curr) => $acc + (float)$curr['total_recaudado'], 0);
                    $granUnidades = array_reduce($ranking, fn($acc, $curr) => $acc + (int)$curr['unidades_vendidas'], 0);
                ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Recaudación Total Dulcería</span>
                        <div class="kpi-report-value text-warning">$<?= number_format($granTotalDulceria, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Unidades Totales Despachadas</span>
                        <div class="kpi-report-value"><?= $granUnidades ?> uds.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Líneas de Menú Analizadas</span>
                        <div class="kpi-report-value"><?= count($ranking) ?> productos</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Producto / Combo</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th class="text-center">Uds. Vendidas</th>
                            <th class="text-end">Ingresos ($)</th>
                            <th class="text-end">% Participación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ranking)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4" style="opacity: 0.6;">No se registraron ventas de
                                dulcería en el período.</td>
                        </tr>
                        <?php else: ?>
                        <?php $pos = 1; foreach ($ranking as $r): ?>
                        <?php
                                        $recaudado = (float)$r['total_recaudado'];
                                        $porc = $granTotalDulceria > 0 ? ($recaudado / $granTotalDulceria) * 100 : 0;
                                    ?>
                        <tr>
                            <td>
                                <?php if ($pos <= 3): ?>
                                <span class="badge bg-warning text-dark fw-bold"><?= $pos ?>°</span>
                                <?php else: ?>
                                <span class="small" style="opacity: 0.6;"><?= $pos ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($r['nombre']) ?></strong></td>
                            <td><span
                                    class="badge bg-secondary text-uppercase"><?= htmlspecialchars($r['categoria']) ?></span>
                            </td>
                            <td>$<?= number_format((float)$r['precio'], 2) ?></td>
                            <td class="text-center fw-bold"><?= (int)$r['unidades_vendidas'] ?></td>
                            <td class="text-end fw-bold text-dorado">$<?= number_format($recaudado, 2) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <span><?= number_format($porc, 1) ?>%</span>
                                    <div class="d-none d-print-none progress"
                                        style="width: 50px; height: 6px; background-color: #333;">
                                        <div class="progress-bar bg-warning" style="width: <?= min(100, $porc) ?>%;">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php $pos++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Reporte de Auditoría de Turnos: Conciliación de fondos iniciales, ventas en gaveta y diferencias de arqueo -->
            <?php elseif ($tipo === 'cortes'): ?>
            <?php
                    $cortesList = $datosReporte['cortes'] ?? [];
                    $totCortes = (int)($datosReporte['total_cortes'] ?? 0);
                    $totCerrados = (int)($datosReporte['total_cerrados'] ?? 0);
                    $totVentas = (float)($datosReporte['total_ventas'] ?? 0);
                    $totFaltantes = (float)($datosReporte['total_faltantes'] ?? 0);
                    $totSobrantes = (float)($datosReporte['total_sobrantes'] ?? 0);
                ?>
            <div class="row g-2 mb-4">
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Turnos Registrados</span>
                        <div class="kpi-report-value"><?= $totCortes ?></div>
                        <small style="font-size: 0.7rem; opacity: 0.7;"><?= $totCerrados ?> cerrados con arqueo</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Ventas Totales Turnos</span>
                        <div class="kpi-report-value text-success">$<?= number_format($totVentas, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Total Faltantes</span>
                        <div class="kpi-report-value text-danger">-$<?= number_format($totFaltantes, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Total Sobrantes</span>
                        <div class="kpi-report-value text-warning">+$<?= number_format($totSobrantes, 2) ?></div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th># Folio</th>
                            <th>Cajero</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Fondo Inicial</th>
                            <th>Ventas</th>
                            <th>Efectivo Declarado</th>
                            <th class="text-end">Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cortesList)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4" style="opacity: 0.6;">No hay turnos registrados en
                                este período.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($cortesList as $c): ?>
                        <?php
                                        $dif = (float)$c['diferencia'];
                                        $estaCerrado = !empty($c['fecha_cierre']);
                                    ?>
                        <tr>
                            <td><strong>#<?= str_pad($c['id_corte'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                            <td><?= htmlspecialchars($c['nombre_cajero']) ?> <small
                                    style="opacity:0.7;">(@<?= htmlspecialchars($c['usuario_cajero']) ?>)</small></td>
                            <td><?= htmlspecialchars($c['inicio_formato']) ?></td>
                            <td><?= $estaCerrado ? htmlspecialchars($c['cierre_formato']) : '<span class="badge bg-warning text-dark">En Curso</span>' ?>
                            </td>
                            <td>$<?= number_format((float)$c['monto_inicial'], 2) ?></td>
                            <td class="text-success fw-bold">$<?= number_format((float)$c['total_ventas'], 2) ?></td>
                            <td><?= $estaCerrado ? '$' . number_format((float)$c['total_declarado'], 2) : 'Pendiente' ?>
                            </td>
                            <td class="text-end">
                                <?php if (!$estaCerrado): ?>
                                <span class="badge bg-secondary">Sin corte</span>
                                <?php elseif (abs($dif) < 0.001): ?>
                                <span class="badge bg-success">Exacto $0.00</span>
                                <?php elseif ($dif < 0): ?>
                                <span class="badge bg-danger">Faltante -$<?= number_format(abs($dif), 2) ?></span>
                                <?php else: ?>
                                <span class="badge bg-warning text-dark">Sobrante +$<?= number_format($dif, 2) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Reporte de Inventario Físico: Existencias en bodega y mostrador, alertas de stock y valuación monetaria -->
            <?php elseif ($tipo === 'inventario'): ?>
            <?php
                    $invProds = $datosReporte['productos'] ?? [];
                    $totArt = (int)($datosReporte['total_articulos'] ?? 0);
                    $totStockUds = (int)($datosReporte['total_stock_unidades'] ?? 0);
                    $totValor = (float)($datosReporte['total_valor_inventario'] ?? 0);
                    $totAgot = (int)($datosReporte['total_agotados'] ?? 0);
                ?>
            <div class="row g-2 mb-4">
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Total Productos</span>
                        <div class="kpi-report-value"><?= $totArt ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Unidades Físicas</span>
                        <div class="kpi-report-value"><?= $totStockUds ?> uds.</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Valorización Total</span>
                        <div class="kpi-report-value text-success">$<?= number_format($totValor, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-report-box">
                        <span class="kpi-report-title">Productos Agotados</span>
                        <div class="kpi-report-value <?= $totAgot > 0 ? 'text-danger' : 'text-success' ?>">
                            <?= $totAgot ?></div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th>Existencias</th>
                            <th>Estado de Stock</th>
                            <th class="text-end">Valorización ($)</th>
                            <th class="text-end">Ventas Históricas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invProds)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4" style="opacity: 0.6;">No hay productos en
                                inventario.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($invProds as $p): ?>
                        <?php $stk = (int)$p['stock_disponible']; ?>
                        <tr>
                            <td><span class="badge bg-secondary">#<?= $p['id_producto'] ?></span></td>
                            <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
                            <td><span
                                    class="badge bg-secondary text-uppercase"><?= htmlspecialchars($p['categoria']) ?></span>
                            </td>
                            <td>$<?= number_format((float)$p['precio'], 2) ?></td>
                            <td><strong><?= $stk ?></strong> uds.</td>
                            <td>
                                <?php if ($stk > 10): ?>
                                <span class="badge bg-success">En Stock</span>
                                <?php elseif ($stk > 0): ?>
                                <span class="badge bg-warning text-dark">Stock Bajo</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Agotado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold text-dorado">$<?= number_format((float)$p['valor_stock'], 2) ?>
                            </td>
                            <td class="text-end"><small><?= (int)($p['total_vendidos'] ?? 0) ?> uds.</small></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>

            <!-- Panel de certificación institucional: código hash SHA-256, folio único y sellos de control interno -->
            <div class="mt-5 pt-3" style="border-top: 1px solid rgba(255,255,255,0.15);">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3"
                            style="background-color: #24242a; border: 1px solid var(--cs-gris-borde);">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                                style="border-color: rgba(255,255,255,0.1) !important;">
                                <span class="fw-bold text-uppercase small text-danger">Certificación de Emisión
                                    Operativa</span>
                                <span
                                    class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50">REGISTRADO
                                    EN SISTEMA</span>
                            </div>
                            <div class="small text-white-50">
                                <div>Responsable: <strong
                                        class="text-white"><?= htmlspecialchars($nombreAdmin) ?></strong>
                                    (<?= htmlspecialchars($nombreRol) ?>)</div>
                                <div>Sello Digital: <code class="text-warning small"><?= $selloHash ?></code></div>
                                <div>Fecha de Sellado: <span class="text-white"><?= date('d/m/Y h:i A') ?></span></div>
                            </div>
                            <div class="p-2 mt-2 rounded text-center small text-white-50"
                                style="background-color: #1a1a1e; border: 1px dashed var(--cs-gris-borde); font-size: 0.72rem;">
                                <i class="bi bi-shield-check text-success me-1"></i> CERTIFICADO DIGITAL DE CONTROL
                                INTERNO — CINE STARK IT
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3"
                            style="background-color: #24242a; border: 1px solid var(--cs-gris-borde);">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                                style="border-color: rgba(255,255,255,0.1) !important;">
                                <span class="fw-bold text-uppercase small text-white">Conformidad y Auditoría
                                    Financiera</span>
                                <span
                                    class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50">CONFORME</span>
                            </div>
                            <div class="small text-white-50">
                                <div>Departamento: <span class="text-white">Gerencia de Administración y Finanzas</span>
                                </div>
                                <div>Protocolo: <span class="text-white">Conciliación de Cierres y Cuadratura</span>
                                </div>
                                <div>Folio de Auditoría: <strong
                                        class="text-white"><?= htmlspecialchars($folio) ?></strong></div>
                            </div>
                            <div class="p-2 mt-2 rounded text-center small text-white-50"
                                style="background-color: #1a1a1e; border: 1px dashed var(--cs-gris-borde); font-size: 0.72rem;">
                                <i class="bi bi-file-earmark-check text-info me-1"></i> VISTO BUENO / ARCHIVO GENERAL DE
                                AUDITORÍA
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3 text-white-50" style="font-size: 0.72rem;">
                    Documento contable oficial generado electrónicamente por la plataforma CineStark v2.0. Válido para
                    auditoría interna y conciliación fiscal.
                </div>
            </div>

        </article>
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>