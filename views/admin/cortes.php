<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var array $cortes */
/** @var array $usuarios */
/** @var string $fechaFiltro */
/** @var int $usuarioFiltro */
/** @var int $totalCortesCerrados */
/** @var float $totalRecaudado */
/** @var float $totalFaltantes */
/** @var float $totalSobrantes */
/** @var int $conteoFaltantes */
/** @var int $conteoSobrantes */
$nombreAdmin = $nombreAdmin ?? 'Administrador';
$nombreRol   = $nombreRol ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría y Cortes de Caja | Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/boleteria.css" rel="stylesheet">
</head>
<body class="pos-body">

    <!-- Header Principal -->
    <header class="pos-header">
        <div class="pos-brand">
            <img src="img/cinestarklogo.png" alt="Cine Stark" class="pos-logo-img">
            <h1 class="pos-title">AUDITORÍA Y CORTES DE CAJA</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRol) ?>: <strong class="pos-user-name"><?= htmlspecialchars($nombreAdmin) ?></strong></span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Toolbar de Navegación Admin -->
    <nav class="pos-toolbar" aria-label="Navegación del Administrador">
        <div class="pos-nav-tabs">
            <a href="index.php?action=admin-dashboard" class="pos-pill"><i class="bi bi-speedometer2 me-1"></i> Resumen</a>
            <a href="index.php?action=admin-peliculas" class="pos-pill"><i class="bi bi-film me-1"></i> Películas</a>
            <a href="index.php?action=admin-salas" class="pos-pill"><i class="bi bi-door-open me-1"></i> Salas</a>
            <a href="index.php?action=admin-funciones" class="pos-pill"><i class="bi bi-calendar2-week me-1"></i> Cartelera</a>
            <a href="index.php?action=admin-usuarios" class="pos-pill"><i class="bi bi-people me-1"></i> Usuarios</a>
            <a href="index.php?action=admin-cortes" class="pos-pill active"><i class="bi bi-cash-stack me-1"></i> Cortes de Caja</a>
            <a href="index.php?action=admin-productos" class="pos-pill"><i class="bi bi-cup-straw me-1"></i> Dulcería</a>
            <a href="index.php?action=admin-reportes" class="pos-pill"><i class="bi bi-file-earmark-bar-graph me-1"></i> Reportes</a>
            <a href="index.php?action=boleteria" class="pos-pill" style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-ticket-perforated me-1"></i> Ir a Boletería</a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="pos-content">

        <!-- Tarjetas KPIs de Resumen -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="sala-card p-3 h-100" style="max-width: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 small text-uppercase fw-semibold">Turnos Auditados</span>
                            <h3 class="fs-2 fw-bold text-white mb-0 mt-1"><?= count($cortes) ?></h3>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                            <i class="bi bi-calendar-check fs-2"></i>
                        </div>
                    </div>
                    <span class="small text-white-50 mt-2 d-inline-block"><?= $totalCortesCerrados ?> cerrados correctamente</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="sala-card p-3 h-100" style="max-width: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 small text-uppercase fw-semibold">Total Ventas Reportadas</span>
                            <h3 class="fs-2 fw-bold text-success mb-0 mt-1">$<?= number_format($totalRecaudado, 2) ?></h3>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(16, 185, 129, 0.15); color: #34d399;">
                            <i class="bi bi-cash-coin fs-2"></i>
                        </div>
                    </div>
                    <span class="small text-white-50 mt-2 d-inline-block">Acumulado en turnos cerrados</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="sala-card p-3 h-100" style="max-width: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 small text-uppercase fw-semibold">Faltantes de Dinero</span>
                            <h3 class="fs-2 fw-bold text-danger mb-0 mt-1">$<?= number_format($totalFaltantes, 2) ?></h3>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(217, 30, 24, 0.15); color: #ff6b6b;">
                            <i class="bi bi-exclamation-octagon fs-2"></i>
                        </div>
                    </div>
                    <span class="small text-white-50 mt-2 d-inline-block"><?= $conteoFaltantes ?> turno(s) con faltante</span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="sala-card p-3 h-100" style="max-width: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 small text-uppercase fw-semibold">Sobrantes de Dinero</span>
                            <h3 class="fs-2 fw-bold text-warning mb-0 mt-1">$<?= number_format($totalSobrantes, 2) ?></h3>
                        </div>
                        <div class="rounded-circle p-3" style="background-color: rgba(245, 185, 33, 0.15); color: var(--cs-dorado);">
                            <i class="bi bi-plus-circle fs-2"></i>
                        </div>
                    </div>
                    <span class="small text-white-50 mt-2 d-inline-block"><?= $conteoSobrantes ?> turno(s) con sobrante</span>
                </div>
            </div>
        </div>

        <!-- Barra de Filtros -->
        <div class="sala-card p-3 mb-4" style="max-width: 100%;">
            <form action="index.php" method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="action" value="admin-cortes">

                <div class="col-md-3">
                    <label class="form-label text-white-50 small mb-1">Filtrar por Fecha</label>
                    <input type="date" name="fecha" value="<?= htmlspecialchars($fechaFiltro) ?>"
                           class="form-control form-control-sm text-white"
                           style="background-color: #1a1a1e; border-color: var(--cs-gris-borde);">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-white-50 small mb-1">Filtrar por Cajero</label>
                    <select name="id_usuario" class="form-select form-select-sm text-white"
                            style="background-color: #1a1a1e; border-color: var(--cs-gris-borde);">
                        <option value="0">-- Todos los cajeros / usuarios --</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id_usuario'] ?>" <?= ($usuarioFiltro === (int)$u['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['usuario']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-danger fw-semibold px-3">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    <?php if ($fechaFiltro !== '' || $usuarioFiltro > 0): ?>
                        <a href="index.php?action=admin-cortes" class="btn btn-sm btn-outline-light px-3">
                            <i class="bi bi-x-circle me-1"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabla de Cortes -->
        <div class="sala-card p-4" style="max-width: 100%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-5 fw-bold text-white mb-0" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 0.5px;">
                    HISTORIAL DE CUADRES Y ARQUEOS (<?= count($cortes) ?> REGISTROS)
                </h3>
                <a href="index.php?action=admin-reportes&tipo=cortes<?= !empty($fechaFiltro) ? '&fecha_inicio=' . urlencode($fechaFiltro) : '' ?><?= !empty($usuarioFiltro) ? '&id_usuario=' . $usuarioFiltro : '' ?>" class="btn btn-sm btn-warning fw-semibold text-dark">
                    <i class="bi bi-printer-fill me-1"></i> Imprimir Reporte de Turnos
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--cs-gris-borde); color: var(--cs-blanco-suave);">
                            <th>Folio</th>
                            <th>Cajero</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Fondo Inicial</th>
                            <th>Ventas Sistema</th>
                            <th>Efectivo Contado</th>
                            <th>Diferencia / Cuadre</th>
                            <th class="text-end">Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cortes)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-white-50 py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    No se encontraron turnos con los filtros seleccionados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cortes as $c): ?>
                                <?php
                                $diferencia = (float)$c['diferencia'];
                                $estaCerrado = !empty($c['fecha_cierre']);
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary font-monospace">#<?= str_pad($c['id_corte'], 5, '0', STR_PAD_LEFT) ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-circle text-white-50"></i>
                                            <div>
                                                <strong class="text-white d-block"><?= htmlspecialchars($c['nombre_cajero']) ?></strong>
                                                <small class="text-white-50">@<?= htmlspecialchars($c['usuario_cajero']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-white-50 d-block"><?= htmlspecialchars($c['inicio_formato']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($estaCerrado): ?>
                                            <small class="text-white d-block"><?= htmlspecialchars($c['cierre_formato']) ?></small>
                                        <?php else: ?>
                                            <span class="badge text-bg-warning">En Curso / Abierto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-white">$<?= number_format((float)$c['monto_inicial'], 2) ?></td>
                                    <td class="fw-semibold text-success">$<?= number_format((float)$c['total_ventas'], 2) ?></td>
                                    <td>
                                        <?php if ($estaCerrado): ?>
                                            <span class="text-white">$<?= number_format((float)$c['total_declarado'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-white-50 small">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$estaCerrado): ?>
                                            <span class="badge bg-secondary">Sin corte</span>
                                        <?php elseif (abs($diferencia) < 0.001): ?>
                                            <span class="badge bg-success" style="background-color: #10b981 !important;">
                                                <i class="bi bi-check-circle me-1"></i> Exacto $0.00
                                            </span>
                                        <?php elseif ($diferencia < 0): ?>
                                            <span class="badge bg-danger" title="Faltante de caja">
                                                <i class="bi bi-dash-circle me-1"></i> Faltante: -$<?= number_format(abs($diferencia), 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge text-bg-warning" title="Sobrante de caja">
                                                <i class="bi bi-plus-circle me-1"></i> Sobrante: +$<?= number_format($diferencia, 2) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="cargarDetalleCorte(<?= (int)$c['id_corte'] ?>)"
                                                title="Ver ticket de arqueo y reimprimir">
                                            <i class="bi bi-receipt me-1"></i> Ver Ticket
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Detalle y Reimpresión de Ticket Térmico -->
    <div class="modal fade" id="modalTicketCorte" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde); color: var(--cs-blanco);">
                <div class="modal-header d-print-none" style="border-bottom: 1px solid var(--cs-gris-borde);">
                    <h5 class="modal-title" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px;">
                        COMPROBANTE DE CORTE DE CAJA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="cargandoTicket" class="text-center py-4">
                        <div class="spinner-border text-warning" role="status"></div>
                        <p class="text-white-50 small mt-2 mb-0">Cargando comprobante térmico...</p>
                    </div>

                    <!-- Ticket Térmico -->
                    <div class="ticket-termico d-none" id="ticketTermicoContenido">
                        <div class="ticket-header">
                            <img src="img/cinestarklogo.png" alt="Cine Stark" class="ticket-logo">
                            <h2 class="ticket-titulo">CORTE DE CAJA</h2>
                            <p class="ticket-sub">Cine Stark S.A. de C.V.</p>
                            <p class="ticket-fecha" id="ticketFechaCierre">--</p>
                        </div>

                        <div class="ticket-linea">================================</div>

                        <div class="ticket-seccion">
                            <div class="ticket-row">
                                <span>CORTE FOLIO #:</span>
                                <strong id="ticketFolio">00000</strong>
                            </div>
                            <div class="ticket-row">
                                <span>CAJERO:</span>
                                <span id="ticketCajero">--</span>
                            </div>
                            <div class="ticket-row">
                                <span>APERTURA:</span>
                                <span id="ticketApertura">--</span>
                            </div>
                            <div class="ticket-row">
                                <span>CIERRE:</span>
                                <span id="ticketCierre">--</span>
                            </div>
                        </div>

                        <div class="ticket-linea">--------------------------------</div>

                        <div class="ticket-seccion">
                            <div class="ticket-row">
                                <span>FONDO INICIAL:</span>
                                <span id="ticketFondo">$ 0.00</span>
                            </div>
                            <div class="ticket-row">
                                <span>TOTAL VENTAS:</span>
                                <strong id="ticketVentas">$ 0.00</strong>
                            </div>
                            <div class="ticket-row">
                                <span>EFECTIVO CONTADO:</span>
                                <span id="ticketDeclarado">$ 0.00</span>
                            </div>
                        </div>

                        <div class="ticket-linea">================================</div>

                        <div class="ticket-seccion">
                            <div class="ticket-row">
                                <span>VENTAS EFECTIVO:</span>
                                <span id="ticketEfectivo">$ 0.00</span>
                            </div>
                            <div class="ticket-row">
                                <span>VENTAS TARJETA:</span>
                                <span id="ticketTarjeta">$ 0.00</span>
                            </div>
                            <div class="ticket-row">
                                <span>CANTIDAD BOLETOS:</span>
                                <span id="ticketBoletos">0</span>
                            </div>
                            <div class="ticket-row">
                                <span>ESPERADO EN GAVETA:</span>
                                <span id="ticketEsperado">$ 0.00</span>
                            </div>
                        </div>

                        <div class="ticket-linea">================================</div>

                        <div class="ticket-total">
                            <span>DIFERENCIA:</span>
                            <span id="ticketDiferencia">$ 0.00</span>
                        </div>
                        <p class="text-center small mb-0 mt-1" id="ticketMensajeCuadre">CUADRE EXACTO</p>

                        <div class="ticket-linea">--------------------------------</div>

                        <div style="margin-top: 25px; text-align: center;">
                            <div style="border-top: 1px solid #333; margin: 20px 20px 4px; padding-top: 4px; font-size: 10px;">
                                Firma Cajero
                            </div>
                            <div style="border-top: 1px solid #333; margin: 25px 20px 4px; padding-top: 4px; font-size: 10px;">
                                Firma Administrador
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-print-none" style="border-top: 1px solid var(--cs-gris-borde);">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-warning fw-semibold" onclick="imprimirModalTicket()">
                        <i class="bi bi-printer-fill me-1"></i> Imprimir Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalTicket = new bootstrap.Modal(document.getElementById('modalTicketCorte'));

        function cargarDetalleCorte(idCorte) {
            document.getElementById('cargandoTicket').classList.remove('d-none');
            document.getElementById('ticketTermicoContenido').classList.add('d-none');
            modalTicket.show();

            fetch('index.php?action=admin-detalle-corte&id=' + encodeURIComponent(idCorte))
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        modalTicket.hide();
                        return;
                    }

                    document.getElementById('ticketFolio').textContent = String(data.id_corte).padStart(5, '0');
                    document.getElementById('ticketCajero').textContent = data.cajero || 'Cajero';
                    document.getElementById('ticketFechaCierre').textContent = data.fecha_cierre || '--';
                    document.getElementById('ticketApertura').textContent = data.fecha_inicio || '--';
                    document.getElementById('ticketCierre').textContent = data.fecha_cierre || 'En curso';

                    document.getElementById('ticketFondo').textContent = '$ ' + Number(data.monto_inicial).toFixed(2);
                    document.getElementById('ticketVentas').textContent = '$ ' + Number(data.total_ventas).toFixed(2);
                    document.getElementById('ticketDeclarado').textContent = '$ ' + Number(data.total_declarado).toFixed(2);

                    document.getElementById('ticketEfectivo').textContent = '$ ' + Number(data.total_efectivo || 0).toFixed(2);
                    document.getElementById('ticketTarjeta').textContent = '$ ' + Number(data.total_tarjeta || 0).toFixed(2);
                    document.getElementById('ticketBoletos').textContent = data.cantidad_boletos || 0;
                    document.getElementById('ticketEsperado').textContent = '$ ' + Number(data.esperado_gaveta || 0).toFixed(2);

                    const dif = Number(data.diferencia);
                    const ticketDif = document.getElementById('ticketDiferencia');
                    const ticketMsg = document.getElementById('ticketMensajeCuadre');

                    if (Math.abs(dif) < 0.001) {
                        ticketDif.textContent = '$ 0.00';
                        ticketMsg.textContent = 'CUADRE EXACTO';
                        ticketMsg.style.color = '#10b981';
                    } else if (dif < 0) {
                        ticketDif.textContent = '-$ ' + Math.abs(dif).toFixed(2);
                        ticketMsg.textContent = 'FALTANTE EN CAJA';
                        ticketMsg.style.color = '#ff6b6b';
                    } else {
                        ticketDif.textContent = '+$ ' + dif.toFixed(2);
                        ticketMsg.textContent = 'SOBRANTE EN CAJA';
                        ticketMsg.style.color = '#f5b921';
                    }

                    document.getElementById('cargandoTicket').classList.add('d-none');
                    document.getElementById('ticketTermicoContenido').classList.remove('d-none');
                })
                .catch(err => {
                    console.error(err);
                    alert('Error al obtener la información del corte de caja.');
                    modalTicket.hide();
                });
        }

        function imprimirModalTicket() {
            const contenido = document.getElementById('ticketTermicoContenido').innerHTML;
            const ventana = window.open('', '_blank', 'width=380,height=600');
            ventana.document.write('<!DOCTYPE html><html><head><title>Imprimir Corte</title>');
            ventana.document.write('<link href="public/css/boleteria.css" rel="stylesheet">');
            ventana.document.write('<style>body { background:#fff!important; color:#000!important; padding:10px; font-family:"Space Mono",monospace; } .ticket-termico { margin:0 auto; box-shadow:none; border:none; color:#000; }</style>');
            ventana.document.write('</head><body><div class="ticket-termico">');
            ventana.document.write(contenido);
            ventana.document.write('</div><script>window.onload = function() { window.print(); window.close(); };<\/script></body></html>');
            ventana.document.close();
        }
    </script>
</body>
</html>
