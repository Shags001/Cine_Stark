<?php
/** @var array $ventaInfo */
/** @var string $nombreCajero */
/** @var string $nombreRol */
$ventaInfo = $ventaInfo ?? [];
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRol = $nombreRol ?? 'Usuario';
$turnoActivo = $turnoActivo ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta #<?= htmlspecialchars($ventaInfo['id_venta']) ?> — Cine Stark</title>

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

    <!-- Contenido Principal: Ticket Térmico -->
    <main class="pos-content py-4">
        <div class="ticket-wrapper">

            <!-- Alerta de Venta Exitosa en Pantalla -->
            <div class="alert alert-success d-print-none text-center mb-4 py-3" style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981;">
                <i class="bi bi-check-circle-fill fs-3 d-block mb-1"></i>
                <h4 class="mb-1 fw-bold">¡Venta Registrada Exitosamente!</h4>
                <p class="mb-0 small">Ticket #<?= htmlspecialchars($ventaInfo['id_venta']) ?> emitido correctamente en el sistema.</p>
            </div>

            <!-- Ticket Térmico 80 mm -->
            <div class="ticket-termico" id="ticketTermico">
                <div class="ticket-header">
                    <img src="img/cinestarklogo.png" alt="Cine Stark" class="ticket-logo">
                    <h2 class="ticket-titulo">CINE STARK TICKET</h2>
                    <p class="ticket-sub">Cine Stark S.A. de C.V.</p>
                    <p class="ticket-fecha"><?= date('d/m/Y h:i A', strtotime($ventaInfo['fecha_hora'])) ?></p>
                </div>

                <div class="ticket-linea">================================</div>

                <div class="ticket-seccion">
                    <div class="ticket-row">
                        <span>TICKET #:</span>
                        <strong><?= str_pad($ventaInfo['id_venta'], 6, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="ticket-row">
                        <span>CAJERO:</span>
                        <span><?= htmlspecialchars($ventaInfo['cajero']) ?></span>
                    </div>
                </div>

                <div class="ticket-linea">--------------------------------</div>

                <div class="ticket-pelicula-box">
                    <div class="ticket-pelicula"><?= htmlspecialchars($ventaInfo['pelicula']) ?></div>
                    <div class="ticket-detalles-funcion">
                        <span><?= htmlspecialchars($ventaInfo['nombre_sala']) ?></span> ·
                        <span><?= htmlspecialchars($ventaInfo['formato']) ?></span> ·
                        <span><?= htmlspecialchars($ventaInfo['horario_formato']) ?></span>
                    </div>
                </div>

                <div class="ticket-linea">--------------------------------</div>

                <div class="ticket-seccion">
                    <div class="ticket-row">
                        <span>BUTACAS:</span>
                        <strong class="ticket-butacas-text"><?= htmlspecialchars(implode(', ', $ventaInfo['butacas'])) ?></strong>
                    </div>
                    <div class="ticket-row">
                        <span>CANTIDAD:</span>
                        <span><?= count($ventaInfo['butacas']) ?> boleto(s)</span>
                    </div>
                    <div class="ticket-row">
                        <span>TARIFA:</span>
                        <span><?= htmlspecialchars($ventaInfo['tarifa'] ?? 'General') ?></span>
                    </div>
                </div>

                <div class="ticket-linea">================================</div>

                <div class="ticket-totales">
                    <div class="ticket-row ticket-total-row">
                        <span>TOTAL:</span>
                        <strong>$ <?= number_format($ventaInfo['total'], 2) ?></strong>
                    </div>
                    <div class="ticket-row">
                        <span>MÉTODO:</span>
                        <span><?= htmlspecialchars($ventaInfo['metodo_pago']) ?></span>
                    </div>
                    <?php if ($ventaInfo['metodo_pago'] === 'Efectivo'): ?>
                        <div class="ticket-row">
                            <span>RECIBIDO:</span>
                            <span>$ <?= number_format($ventaInfo['efectivo_recibido'] ?? $ventaInfo['total'], 2) ?></span>
                        </div>
                        <div class="ticket-row">
                            <span>CAMBIO:</span>
                            <span>$ <?= number_format($ventaInfo['cambio'] ?? 0, 2) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="ticket-linea">--------------------------------</div>

                <!-- Código de validación para control de acceso -->
                <div class="ticket-validacion">
                    <div class="ticket-codigo-box">
                        <span>CÓDIGO DE ACCESO:</span>
                        <div class="ticket-codigo">STK-<?= str_pad($ventaInfo['id_venta'], 5, '0', STR_PAD_LEFT) ?>-<?= strtoupper(substr(md5($ventaInfo['id_venta'] . 'CINESTARK'), 0, 4)) ?></div>
                    </div>
                    <p class="ticket-nota">Presente este boleto en el acceso a sala.<br>¡Disfrute su función!</p>
                </div>
            </div>

            <!-- Botones de Acción en Pantalla -->
            <div class="ticket-acciones d-print-none mt-4">
                <button type="button" class="btn btn-outline-light px-4 py-2" onclick="window.print();">
                    <i class="bi bi-printer-fill me-2"></i> Imprimir Ticket (80 mm)
                </button>
                <a href="index.php?action=boleteria" class="btn-stark px-4 py-2 text-decoration-none d-inline-block text-center" style="width: auto;">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Venta
                </a>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Autoimpresión al cargar si se desea, o manual mediante el botón
    </script>
</body>
</html>
