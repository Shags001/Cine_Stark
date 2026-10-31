<?php
/** @var array $corteInfo */
/** @var string $nombreCajero */
/** @var string $nombreRol */
$corteInfo = $corteInfo ?? [];
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRol = $nombreRol ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja #<?= htmlspecialchars($corteInfo['id_corte']) ?> — Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/boleteria.css" rel="stylesheet">
</head>
<body class="pos-body">

    <!-- Header Principal -->
    <header class="pos-header d-print-none">
        <div class="pos-brand">
            <img src="img/cinestarklogo.png" alt="Cine Stark" class="pos-logo-img">
            <h1 class="pos-title">CIERRE DE CAJA</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRol) ?>: <strong class="pos-user-name"><?= htmlspecialchars($nombreCajero) ?></strong> · <span class="text-danger">Turno cerrado</span></span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <main class="pos-content py-4">
        <div class="ticket-wrapper">

            <!-- Alerta en Pantalla -->
            <div class="alert alert-success d-print-none text-center mb-4 py-3" style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981;">
                <i class="bi bi-shield-check fs-3 d-block mb-1"></i>
                <h4 class="mb-1 fw-bold">¡Caja Cerrada y Registrada!</h4>
                <p class="mb-0 small">Corte de caja #<?= htmlspecialchars($corteInfo['id_corte']) ?> guardado correctamente en el historial.</p>
            </div>

            <!-- Ticket Térmico de Corte de Caja -->
            <div class="ticket-termico" id="ticketCorte">
                <div class="ticket-header">
                    <img src="img/cinestarklogo.png" alt="Cine Stark" class="ticket-logo">
                    <h2 class="ticket-titulo">CORTE DE CAJA</h2>
                    <p class="ticket-sub">Cine Stark S.A. de C.V.</p>
                    <p class="ticket-fecha"><?= date('d/m/Y h:i A', strtotime($corteInfo['fecha_cierre'])) ?></p>
                </div>

                <div class="ticket-linea">================================</div>

                <div class="ticket-seccion">
                    <div class="ticket-row">
                        <span>CORTE FOLIO #:</span>
                        <strong><?= str_pad($corteInfo['id_corte'], 5, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="ticket-row">
                        <span>CAJERO:</span>
                        <span><?= htmlspecialchars($corteInfo['cajero']) ?></span>
                    </div>
                    <div class="ticket-row">
                        <span>APERTURA:</span>
                        <span><?= date('d/m/y h:i A', strtotime($corteInfo['fecha_inicio'])) ?></span>
                    </div>
                    <div class="ticket-row">
                        <span>CIERRE:</span>
                        <span><?= date('d/m/y h:i A', strtotime($corteInfo['fecha_cierre'])) ?></span>
                    </div>
                </div>

                <div class="ticket-linea">--------------------------------</div>

                <div class="ticket-seccion">
                    <div class="ticket-row">
                        <span>FONDO INICIAL:</span>
                        <span>$ <?= number_format($corteInfo['monto_inicial'], 2) ?></span>
                    </div>
                    <div class="ticket-row">
                        <span>TOTAL VENTAS:</span>
                        <strong>$ <?= number_format($corteInfo['total_ventas'], 2) ?></strong>
                    </div>
                    <?php if (isset($corteInfo['total_boletos']) || isset($corteInfo['total_dulceria'])): ?>
                        <div class="ticket-row" style="font-size: 0.72rem; color: #555; padding-left: 8px;">
                            <span>• BOLETERÍA:</span>
                            <span>$ <?= number_format($corteInfo['total_boletos'] ?? 0, 2) ?></span>
                        </div>
                        <div class="ticket-row" style="font-size: 0.72rem; color: #555; padding-left: 8px;">
                            <span>• DULCERÍA:</span>
                            <span>$ <?= number_format($corteInfo['total_dulceria'] ?? 0, 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="ticket-row">
                        <span>EFECTIVO CONTADO:</span>
                        <span>$ <?= number_format($corteInfo['total_declarado'], 2) ?></span>
                    </div>
                </div>

                <div class="ticket-linea">================================</div>

                <div class="ticket-seccion">
                    <div class="ticket-row ticket-total-row">
                        <span>DIFERENCIA:</span>
                        <strong style="color: <?= (float)$corteInfo['diferencia'] < 0 ? '#b91c1c' : ((float)$corteInfo['diferencia'] > 0 ? '#b45309' : '#047857') ?>;">
                            <?= (float)$corteInfo['diferencia'] >= 0 ? '+' : '' ?>$ <?= number_format($corteInfo['diferencia'], 2) ?>
                        </strong>
                    </div>
                    <div class="ticket-row">
                        <span>ESTADO:</span>
                        <span>
                            <?php if (abs((float)$corteInfo['diferencia']) < 0.01): ?>
                                CUADRADO EXACTO
                            <?php elseif ((float)$corteInfo['diferencia'] < 0): ?>
                                FALTANTE EN CAJA
                            <?php else: ?>
                                SOBRANTE EN CAJA
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <div class="ticket-linea">--------------------------------</div>

                <!-- Firmas de responsabilidad -->
                <div style="margin-top: 25px; text-align: center;">
                    <div style="border-top: 1px solid #333; margin: 20px 20px 4px; padding-top: 4px; font-size: 10px;">
                        Firma Cajero
                    </div>
                    <div style="border-top: 1px solid #333; margin: 25px 20px 4px; padding-top: 4px; font-size: 10px;">
                        Firma Administrador
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="ticket-acciones d-print-none mt-4">
                <button type="button" class="btn btn-outline-light px-4 py-2" onclick="window.print();">
                    <i class="bi bi-printer-fill me-2"></i> Imprimir Corte
                </button>
                <a href="index.php?action=apertura-caja" class="btn-stark px-4 py-2 text-decoration-none d-inline-block text-center" style="width: auto;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Abrir Nuevo Turno
                </a>
                <?php if (isset($_SESSION['id_rol']) && ((int)$_SESSION['id_rol'] === 1 || stripos($_SESSION['nombre_rol'] ?? '', 'admin') !== false)): ?>
                    <a href="index.php?action=admin-dashboard" class="btn btn-outline-warning px-4 py-2 text-decoration-none d-inline-block text-center" style="width: auto;">
                        <i class="bi bi-gear-fill me-1"></i> Administración
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
