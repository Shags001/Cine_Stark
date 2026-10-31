<?php
/** @var string $nombreCajero */
/** @var string $nombreRolStr */
/** @var array|null $turnoActivo */
$nombreCajero = $nombreCajero ?? 'Cajero';
$nombreRolStr = $nombreRolStr ?? 'Usuario';
$turnoActivo = $turnoActivo ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta — Dulcería | Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/boleteria.css" rel="stylesheet">
    <link href="public/css/dulceria.css" rel="stylesheet">
</head>
<body class="pos-body">

    <!-- Header Principal -->
    <header class="pos-header">
        <div class="pos-brand">
            <img src="img/cinestarklogo.png" alt="Cine Stark" class="pos-logo-img">
            <h1 class="pos-title">DULCERÍA · PUNTO DE VENTA</h1>
        </div>
        <div class="pos-user-info">
            <span><?= htmlspecialchars($nombreRolStr) ?>: <strong class="pos-user-name"><?= htmlspecialchars($nombreCajero) ?></strong> · 
                <?php if (!empty($turnoActivo['id_corte'])): ?>
                    <span class="pos-status-badge">Turno #<?= (int)$turnoActivo['id_corte'] ?> abierto</span>
                <?php else: ?>
                    <span class="pos-status-badge bg-danger text-white border-0"><i class="bi bi-lock-fill me-1"></i> Sin Turno Activo</span>
                <?php endif; ?>
            </span>
            <a href="index.php?action=logout" class="btn-logout-link" title="Cerrar sesión actual">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Toolbar de Navegación -->
    <nav class="pos-toolbar" aria-label="Navegación de módulos">
        <div class="pos-nav-tabs">
            <a href="index.php?action=boleteria" class="pos-pill"><i class="bi bi-ticket-perforated me-1"></i> Boletería</a>
            <a href="index.php?action=dulceria" class="pos-pill active"><i class="bi bi-cup-straw me-1"></i> Dulcería</a>
            <a href="index.php?action=cierre-caja" class="pos-pill"><i class="bi bi-cash-stack me-1"></i> Cierre de caja</a>
            <?php if (isset($_SESSION['id_rol']) && ((int)$_SESSION['id_rol'] === 1 || stripos($_SESSION['nombre_rol'] ?? '', 'admin') !== false)): ?>
                <a href="index.php?action=admin-dashboard" class="pos-pill" style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-gear-fill me-1"></i> Administración</a>
            <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($turnoActivo['id_corte'])): ?>
                <span class="badge" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde); color: var(--cs-blanco-suave); font-size: 0.85rem; padding: 0.45rem 0.85rem;">
                    <i class="bi bi-shop me-1 text-warning"></i> Mostrador Principal · Turno Habilitado
                </span>
            <?php else: ?>
                <a href="index.php?action=apertura-caja" class="btn btn-sm btn-warning fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Abrir Turno Requerido
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Contenido Principal POS Dulcería -->
    <main class="pos-content">
        <?php if (empty($turnoActivo['id_corte'])): ?>
            <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4" style="background-color: rgba(217, 30, 24, 0.15); border: 1px solid var(--cs-rojo); color: #fff;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lock-fill text-danger fs-3"></i>
                    <div>
                        <strong class="text-danger">VENTAS BLOQUEADAS: No tiene un turno de caja abierto.</strong>
                        <div class="small text-white-50">Por política de seguridad y auditoría de Cine Stark, debe registrar el fondo inicial de caja antes de realizar ventas.</div>
                    </div>
                </div>
                <a href="index.php?action=apertura-caja" class="btn btn-warning fw-semibold px-3 py-2 text-dark">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Realizar Apertura de Caja
                </a>
            </div>
        <?php endif; ?>
        <div class="dulceria-container">

            <!-- Columna Izquierda: Catálogo y Filtros -->
            <div>
                <!-- Barra de Búsqueda y Filtros Rápidos -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-5">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y text-white-50" style="left: 14px;"></i>
                            <input type="text" id="buscador" class="search-input-box ps-5" placeholder="Buscar combo, palomitas, bebidas...">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex gap-2 overflow-x-auto pb-1" id="contenedorCategorias">
                            <span class="cat-pill active" data-cat="todos"><i class="bi bi-grid-fill"></i> Todos</span>
                            <span class="cat-pill" data-cat="combos"><i class="bi bi-stars text-warning"></i> Combos</span>
                            <span class="cat-pill" data-cat="palomitas"><i class="bi bi-box2"></i> Palomitas</span>
                            <span class="cat-pill" data-cat="bebidas"><i class="bi bi-cup-straw"></i> Bebidas</span>
                            <span class="cat-pill" data-cat="snacks"><i class="bi bi-fire"></i> Snacks</span>
                            <span class="cat-pill" data-cat="dulces"><i class="bi bi-bag-heart"></i> Golosinas</span>
                        </div>
                    </div>
                </div>

                <!-- Grid de Productos -->
                <div class="productos-grid" id="gridProductos">
                    <!-- Renderizado dinámico vía JavaScript -->
                </div>
            </div>

            <!-- Columna Derecha: Orden en Curso (Comanda / Carrito) -->
            <div>
                <div class="orden-panel">
                    <div class="orden-header">
                        <div>
                            <h5 class="text-white mb-0 fw-bold" style="letter-spacing: 0.5px;">
                                <i class="bi bi-receipt me-1 text-danger"></i> ORDEN ACTUAL
                            </h5>
                            <span class="text-white-50 small" id="contadorItemsTexto">0 productos seleccionados</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnVaciarOrden" title="Vaciar orden completa" onclick="vaciarOrden()">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <!-- Lista de Ítems en el Carrito -->
                    <div class="orden-lista" id="listaOrden">
                        <!-- Renderizado dinámico de items -->
                        <div class="text-center text-white-50 py-5 my-auto" id="ordenVaciaMensaje">
                            <i class="bi bi-basket3 display-4 d-block mb-2" style="opacity: 0.35;"></i>
                            <p class="mb-0 small">No hay productos agregados.</p>
                            <span style="font-size: 0.72rem; opacity: 0.6;">Toca cualquier producto o combo del catálogo para sumarlo al pedido.</span>
                        </div>
                    </div>

                    <!-- Resumen Financiero y Botón de Cobro -->
                    <div class="orden-footer">
                        <div class="d-flex justify-content-between text-white-50 small mb-1">
                            <span>Subtotal</span>
                            <span id="resumenSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between text-white-50 small mb-3">
                            <span>IVA Incluido (13%)</span>
                            <span id="resumenIva">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pt-2" style="border-top: 1px dashed rgba(255,255,255,0.15);">
                            <span class="text-white fw-bold">TOTAL A COBRAR:</span>
                            <span class="text-warning fw-bold fs-2" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px;" id="resumenTotal">$0.00</span>
                        </div>

                        <button type="button" class="btn-cobrar-pos" id="btnAbrirCobro" onclick="abrirModalCobro()" disabled>
                            <i class="bi bi-credit-card-2-front me-1"></i> COBRAR ORDEN
                            <span class="badge bg-dark text-warning ms-2" style="font-size: 0.75rem; border: 1px solid rgba(245,185,33,0.4);">F2</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal de Personalización de Producto (Sabores y Extras) -->
    <div class="modal fade" id="modalPersonalizar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--cs-negro); border: 1px solid var(--cs-gris-borde); color: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-sliders2 fs-4 text-warning"></i>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalPersTitulo">PERSONALIZAR PRODUCTO</h5>
                            <span class="text-white-50 small" id="modalPersSubtitulo">Seleccione sabor y agregados opcionales</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Resumen cabecera con foto y precio base -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde);">
                        <div style="width: 70px; height: 70px; border-radius: 8px; overflow: hidden; background-color: #19191d; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" id="modalPersImgBox">
                            <!-- Img dinámica -->
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-1 fs-5" id="modalPersNombreProducto">Palomitas</h6>
                            <span class="text-white-50 small">Precio Base: <strong class="text-warning" id="modalPersPrecioBase">$0.00</strong></span>
                        </div>
                    </div>

                    <!-- Sección 1: Selección de Sabor -->
                    <div class="mb-4" id="modalPersSeccionSabores">
                        <label class="form-label text-white-50 small text-uppercase fw-semibold d-block">
                            1. Sabor / Variedad *
                        </label>
                        <div class="d-flex flex-column gap-2" id="modalPersOpcionesSabores">
                            <!-- Render dinámico -->
                        </div>
                    </div>

                    <!-- Sección 2: Extras opcionales -->
                    <div class="mb-4" id="modalPersSeccionExtras">
                        <label class="form-label text-white-50 small text-uppercase fw-semibold d-block">
                            2. Extras / Agregados (Opcional)
                        </label>
                        <div class="d-flex flex-column gap-2" id="modalPersOpcionesExtras">
                            <!-- Render dinámico -->
                        </div>
                    </div>

                    <!-- Sección 3: Cantidad a Llevar -->
                    <div class="mb-2">
                        <label for="modalPersCantidadInput" class="form-label text-white-50 small text-uppercase fw-semibold d-block">
                            3. Cantidad a Llevar *
                        </label>
                        <div style="max-width: 120px;">
                            <input type="number" min="1" max="999" id="modalPersCantidadInput" class="form-control text-center fw-bold fs-4" value="1" style="height: 46px; background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" oninput="actualizarCantidadModalDesdeInput(this.value)" onblur="if(this.value === '' || parseInt(this.value) < 1) { this.value = '1'; calcularTotalPersonalizado(); }">
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between align-items-center" style="border-top: 1px solid var(--cs-gris-borde);">
                    <div>
                        <span class="text-white-50 small d-block" id="modalPersLabelCalculo">Total (1 unidad):</span>
                        <span class="fs-2 fw-bold text-warning" style="font-family: 'Bebas Neue', sans-serif;" id="modalPersTotal">$0.00</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar <span class="small opacity-50 ms-1">(Esc)</span></button>
                        <button type="button" class="btn btn-danger fw-semibold px-4" id="btnConfirmarPers" onclick="confirmarAgregarPersonalizado()">
                            <i class="bi bi-cart-plus me-1"></i> <span id="modalPersBtnTexto">Agregar a la Orden</span>
                            <span class="badge bg-dark ms-2" style="font-size: 0.72rem; border: 1px solid rgba(255,255,255,0.2);">Enter ↵</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Cobro Rápido -->
    <div class="modal fade" id="modalCobro" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--cs-negro); border: 1px solid var(--cs-gris-borde); color: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="bi bi-cash-coin me-2 text-warning"></i> PROCESAR COBRO DULCERÍA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Total Destacado -->
                    <div class="text-center p-3 rounded-3 mb-4" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde);">
                        <span class="text-white-50 small text-uppercase fw-semibold">Total a Liquidar</span>
                        <div class="text-warning fw-bold display-5 my-1" style="font-family: 'Bebas Neue', sans-serif;" id="modalMontoTotal">$0.00</div>
                        <span class="badge bg-danger" id="modalCantidadItemsBadge">0 productos</span>
                    </div>

                    <!-- Selector de Método de Pago -->
                    <label class="form-label text-white-50 small fw-semibold">Método de Pago *</label>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="metodo-pago-card active" id="cardMetodoEfectivo" onclick="seleccionarMetodoPago('efectivo')">
                                <i class="bi bi-cash-stack fs-2 text-success d-block mb-1"></i>
                                <span class="fw-bold">Efectivo</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metodo-pago-card" id="cardMetodoTarjeta" onclick="seleccionarMetodoPago('tarjeta')">
                                <i class="bi bi-credit-card fs-2 text-info d-block mb-1"></i>
                                <span class="fw-bold">Tarjeta</span>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Efectivo -->
                    <div id="seccionEfectivo">
                        <label class="form-label text-white-50 small fw-semibold">Monto Recibido del Cliente ($)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="background-color: #2a2a30; border-color: var(--cs-gris-borde); color: var(--cs-dorado); font-weight: 700;">$</span>
                            <input type="number" step="0.25" min="0" id="inputEfectivoRecibido" class="form-control form-control-lg fw-bold" placeholder="0.00" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" oninput="calcularCambio()">
                        </div>

                        <!-- Botones de Billetes Rápidos -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-billete flex-grow-1" onclick="setBilleteExacto()"><i class="bi bi-bullseye me-1"></i> Exacto <small class="text-white-50 ms-1">(4)</small></button>
                            <button type="button" class="btn btn-billete flex-grow-1" onclick="setBillete(5)">$5 <small class="text-white-50 ms-1">(1)</small></button>
                            <button type="button" class="btn btn-billete flex-grow-1" onclick="setBillete(10)">$10 <small class="text-white-50 ms-1">(2)</small></button>
                            <button type="button" class="btn btn-billete flex-grow-1" onclick="setBillete(20)">$20 <small class="text-white-50 ms-1">(3)</small></button>
                            <button type="button" class="btn btn-billete flex-grow-1" onclick="setBillete(50)">$50</button>
                        </div>

                        <!-- Caja de Vuelto / Cambio -->
                        <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" id="boxCambio" style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid #10b981;">
                            <span class="fw-semibold text-white">Cambio / Vuelto:</span>
                            <span class="fs-3 fw-bold text-success" id="textoCambio" style="font-family: 'Bebas Neue', sans-serif;">$0.00</span>
                        </div>
                    </div>

                    <!-- Panel de Tarjeta -->
                    <div id="seccionTarjeta" class="d-none">
                        <div class="alert alert-info py-3 mb-0" style="background-color: rgba(13, 202, 240, 0.1); border-color: #0dcaf0; color: #b6effb;">
                            <i class="bi bi-info-circle-fill me-2"></i> Solicite la tarjeta al cliente e ingrese el cobro en la terminal bancaria (POS).
                        </div>
                        <div class="mt-3">
                            <label class="form-label text-white-50 small">Últimos 4 dígitos o Núm. de Autorización (opcional):</label>
                            <input type="text" id="inputNumAutorizacion" class="form-control" placeholder="Ej. 4589 o AUT-8841" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar <span class="small opacity-50 ms-1">(Esc)</span></button>
                    <button type="button" class="btn btn-danger fw-semibold px-4" id="btnConfirmarVenta" onclick="finalizarVenta()">
                        <i class="bi bi-check-circle me-1"></i> Confirmar y Generar Ticket
                        <span class="badge bg-dark ms-2" style="font-size: 0.72rem; border: 1px solid rgba(255,255,255,0.2);">Enter ↵</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Ticket Térmico Simulado -->
    <div class="modal fade" id="modalTicket" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--cs-negro); border: 1px solid var(--cs-gris-borde); color: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="bi bi-printer-fill me-2 text-warning"></i> COMPROBANTE DE DULCERÍA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="resetearParaNuevaVenta()"></button>
                </div>

                <div class="modal-body p-3">
                    <!-- Ticket Impreso -->
                    <div class="ticket-modal-paper" id="ticketImprimible">
                        <div class="text-center mb-2">
                            <div style="font-size: 1.25rem; font-weight: 900; letter-spacing: 1px;">CINE STARK</div>
                            <div style="font-size: 0.72rem;">MOSTRADOR DULCERÍA / CONCESIONES</div>
                            <div style="font-size: 0.68rem; color: #555;">NIT: 0614-281026-101-3 · IVA: 248910-1</div>
                        </div>

                        <div style="border-top: 1px dashed #333; border-bottom: 1px dashed #333; padding: 4px 0; font-size: 0.72rem; margin-bottom: 8px;">
                            <div>Ticket: <strong id="ticketNumOrden">#D-1001</strong></div>
                            <div>Fecha: <span id="ticketFecha"><?= date('d/m/Y H:i A') ?></span></div>
                            <div>Atendido por: <?= htmlspecialchars($nombreCajero) ?></div>
                        </div>

                        <table style="width: 100%; font-size: 0.72rem; margin-bottom: 8px;">
                            <thead>
                                <tr style="border-bottom: 1px solid #ddd; text-align: left;">
                                    <th>CANT</th>
                                    <th>DESCRIPCIÓN</th>
                                    <th style="text-align: right;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody id="ticketTablaItems">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>

                        <div style="border-top: 1px dashed #333; padding-top: 6px; font-size: 0.75rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>SUBTOTAL:</span>
                                <span id="ticketSubtotal">$0.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>IVA INCLUIDO (13%):</span>
                                <span id="ticketIva">$0.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 900; font-size: 0.95rem; margin-top: 4px; border-top: 1px solid #111; padding-top: 4px;">
                                <span>TOTAL PAGADO:</span>
                                <span id="ticketTotal">$0.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #555; margin-top: 4px;">
                                <span>Método de Pago:</span>
                                <span id="ticketMetodo">EFECTIVO</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #555;" id="ticketRowEfectivo">
                                <span>Recibido / Cambio:</span>
                                <span id="ticketRecibidoCambio">$0.00 / $0.00</span>
                            </div>
                        </div>

                        <!-- Código de Canje / Mostrador -->
                        <div class="text-center mt-3 pt-2" style="border-top: 1px dashed #333;">
                            <div style="font-size: 0.65rem; color: #444;">ENTREGUE EN BARRA DE ENTREGA</div>
                            <div style="font-size: 1.1rem; font-weight: 900; letter-spacing: 2px; margin: 4px 0;" id="ticketCodigoEntrega">#CANJE-8821</div>
                            <div style="font-size: 0.62rem; color: #666;">¡Gracias por preferir Cine Stark! Disfruta la función.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                    <button type="button" class="btn btn-outline-light" onclick="imprimirTicket()">
                        <i class="bi bi-printer me-1"></i> Imprimir Ticket
                        <span class="badge bg-dark text-white-50 ms-2" style="font-size: 0.72rem; border: 1px solid rgba(255,255,255,0.2);">Enter ↵</span>
                    </button>
                    <button type="button" class="btn btn-danger fw-semibold px-4" data-bs-dismiss="modal" onclick="resetearParaNuevaVenta()">
                        <i class="bi bi-plus-circle me-1"></i> Nueva Venta
                        <span class="badge bg-dark text-white-50 ms-2" style="font-size: 0.72rem; border: 1px solid rgba(255,255,255,0.2);">Esc</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Lógica Interactiva del POS de Dulcería -->
    <script>
        // Catálogo dinámico cargado desde la base de datos MySQL (sincronizado en tiempo real con el CRUD de Admin)
        const PRODUCTOS = <?= !empty($catalogoPos) ? json_encode($catalogoPos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) : '[]' ?>;
        const tieneTurnoActivo = <?= !empty($turnoActivo['id_corte']) ? 'true' : 'false' ?>;

        // Estado del Carrito y Filtros
        let carrito = []; // { key, idProducto, nombre, sabor, extras, precio, cantidad }
        let categoriaSeleccionada = 'todos';
        let terminoBusqueda = '';
        let metodoPagoActual = 'efectivo';

        // Variables temporales para personalización
        let productoEnPersonalizacion = null;
        let saborSeleccionado = null;
        let extrasSeleccionados = [];

        // Elementos DOM
        const gridProductos = document.getElementById('gridProductos');
        const listaOrden = document.getElementById('listaOrden');
        const ordenVaciaMensaje = document.getElementById('ordenVaciaMensaje');
        const contadorItemsTexto = document.getElementById('contadorItemsTexto');
        const resumenSubtotal = document.getElementById('resumenSubtotal');
        const resumenIva = document.getElementById('resumenIva');
        const resumenTotal = document.getElementById('resumenTotal');
        const btnAbrirCobro = document.getElementById('btnAbrirCobro');

        // Modales Bootstrap
        const modalCobro = new bootstrap.Modal(document.getElementById('modalCobro'));
        const modalTicket = new bootstrap.Modal(document.getElementById('modalTicket'));
        const modalPersonalizar = new bootstrap.Modal(document.getElementById('modalPersonalizar'));

        // Renderizar Catálogo de Productos
        function renderizarCatalogo() {
            gridProductos.innerHTML = '';

            const productosFiltrados = PRODUCTOS.filter(p => {
                const matchCategoria = (categoriaSeleccionada === 'todos') || (p.categoria === categoriaSeleccionada);
                const matchBusqueda = p.nombre.toLowerCase().includes(terminoBusqueda.toLowerCase()) ||
                                      p.descripcion.toLowerCase().includes(terminoBusqueda.toLowerCase());
                return matchCategoria && matchBusqueda;
            });

            if (productosFiltrados.length === 0) {
                gridProductos.innerHTML = `
                    <div class="col-12 text-center text-white-50 py-5" style="grid-column: 1 / -1;">
                        <i class="bi bi-search display-5 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">No se encontraron productos que coincidan.</p>
                    </div>
                `;
                return;
            }

            productosFiltrados.forEach(prod => {
                // Cantidad total agregada de este producto (sumando variantes)
                const cant = carrito
                    .filter(item => item.idProducto === prod.id)
                    .reduce((acc, curr) => acc + curr.cantidad, 0);

                const card = document.createElement('div');
                card.className = `producto-card ${cant > 0 ? 'en-carrito' : ''}`;
                card.onclick = () => clickProducto(prod.id);

                card.innerHTML = `
                    ${cant > 0 ? `<span class="badge-cantidad">${cant} en orden</span>` : ''}
                    <div>
                        <div class="producto-imagen-box">
                            <img src="${prod.imagen}" alt="${prod.nombre}" class="producto-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="producto-img-placeholder" style="display: none;">
                                <i class="bi ${prod.iconoPlaceholder}"></i>
                                <span>Sin Foto</span>
                            </div>
                        </div>
                        <div class="producto-categoria-tag">${prod.categoria}</div>
                        <h4 class="producto-titulo">${prod.nombre}</h4>
                        <p class="producto-desc">${prod.descripcion}</p>
                        ${prod.personalizable ? `
                            <div class="badge-personalizable">
                                <i class="bi bi-sliders"></i> Sabores y Extras
                            </div>
                        ` : ''}
                    </div>
                    <div class="producto-precio-row">
                        <div>
                            <span class="producto-precio">$${prod.precio.toFixed(2)}</span>
                            ${prod.personalizable ? '<span class="text-white-50 small ms-1" style="font-size:0.75rem;">base</span>' : ''}
                        </div>
                        <button type="button" class="btn-agregar-rapido" title="Agregar a la orden" onclick="event.stopPropagation(); clickProducto(${prod.id});">
                            <i class="${prod.personalizable ? 'bi bi-sliders' : 'bi bi-plus-lg'}"></i>
                        </button>
                    </div>
                `;
                gridProductos.appendChild(card);
            });
        }

        // Manejo del Clic en Producto
        function clickProducto(idProducto, forzarModal = false) {
            const prod = PRODUCTOS.find(p => p.id === idProducto);
            if (!prod) return;

            // Abre el modal para configurar cantidad (y sabores/extras si aplica)
            abrirModalPersonalizar(prod);
        }

        // Modal de Personalización y Selección de Cantidad
        function abrirModalPersonalizar(prod) {
            productoEnPersonalizacion = prod;
            saborSeleccionado = prod.sabores && prod.sabores.length > 0 ? (prod.sabores.find(s => s.default) || prod.sabores[0]) : null;
            extrasSeleccionados = [];

            // Resetear cantidad a 1
            document.getElementById('modalPersCantidadInput').value = '1';

            document.getElementById('modalPersTitulo').textContent = prod.personalizable 
                ? `PERSONALIZAR ${prod.nombre.toUpperCase()}`
                : `AGREGAR ${prod.nombre.toUpperCase()}`;
            document.getElementById('modalPersSubtitulo').textContent = prod.personalizable
                ? 'Seleccione sabor, agregados opcionales y cantidad'
                : 'Indique la cantidad que desea agregar a la orden';
            document.getElementById('modalPersNombreProducto').textContent = prod.nombre;
            document.getElementById('modalPersPrecioBase').textContent = `$${prod.precio.toFixed(2)}`;

            // Imagen miniatura
            const imgBox = document.getElementById('modalPersImgBox');
            imgBox.innerHTML = `
                <img src="${prod.imagen}" alt="${prod.nombre}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="producto-img-placeholder" style="display:none;"><i class="bi ${prod.iconoPlaceholder}"></i></div>
            `;

            // Renderizar Sabores
            const secSabores = document.getElementById('modalPersSeccionSabores');
            const contSabores = document.getElementById('modalPersOpcionesSabores');
            contSabores.innerHTML = '';
            if (prod.sabores && prod.sabores.length > 0) {
                secSabores.style.display = 'block';
                prod.sabores.forEach(sabor => {
                    const isActivo = saborSeleccionado && saborSeleccionado.nombre === sabor.nombre;
                    const card = document.createElement('div');
                    card.className = `opt-sabor-card ${isActivo ? 'active' : ''}`;
                    card.onclick = () => {
                        saborSeleccionado = sabor;
                        document.querySelectorAll('.opt-sabor-card').forEach(c => {
                            c.classList.remove('active');
                            const ic = c.querySelector('.icon-radio');
                            if (ic) ic.className = 'bi bi-circle text-white-50 icon-radio';
                        });
                        card.classList.add('active');
                        const myIc = card.querySelector('.icon-radio');
                        if (myIc) myIc.className = 'bi bi-record-circle text-warning icon-radio';
                        calcularTotalPersonalizado();
                    };
                    card.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi ${isActivo ? 'bi-record-circle text-warning' : 'bi-circle text-white-50'} icon-radio"></i>
                            <span class="fw-semibold">${sabor.nombre}</span>
                        </div>
                        <span class="small ${sabor.extra > 0 ? 'text-warning fw-bold' : 'text-white-50'}">
                            ${sabor.extra > 0 ? `+ $${sabor.extra.toFixed(2)}` : 'Incluido'}
                        </span>
                    `;
                    contSabores.appendChild(card);
                });
            } else {
                secSabores.style.display = 'none';
            }

            // Renderizar Extras
            const secExtras = document.getElementById('modalPersSeccionExtras');
            const contExtras = document.getElementById('modalPersOpcionesExtras');
            contExtras.innerHTML = '';
            if (prod.extras && prod.extras.length > 0) {
                secExtras.style.display = 'block';
                prod.extras.forEach(extra => {
                    const card = document.createElement('div');
                    card.className = 'opt-extra-card';
                    card.onclick = () => {
                        const existeIdx = extrasSeleccionados.findIndex(e => e.nombre === extra.nombre);
                        const iconCheck = card.querySelector('.icon-check');
                        if (existeIdx >= 0) {
                            extrasSeleccionados.splice(existeIdx, 1);
                            card.classList.remove('active');
                            iconCheck.className = 'bi bi-square text-white-50 icon-check';
                        } else {
                            extrasSeleccionados.push(extra);
                            card.classList.add('active');
                            iconCheck.className = 'bi bi-check-square-fill text-warning icon-check';
                        }
                        calcularTotalPersonalizado();
                    };
                    card.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-square text-white-50 icon-check"></i>
                            <span class="fw-semibold">${extra.nombre}</span>
                        </div>
                        <span class="small text-warning fw-bold">+ $${extra.precio.toFixed(2)}</span>
                    `;
                    contExtras.appendChild(card);
                });
            } else {
                secExtras.style.display = 'none';
            }

            calcularTotalPersonalizado();
            modalPersonalizar.show();
        }

        // Controles de Cantidad en Modal
        function cambiarCantidadModal(delta) {
            const input = document.getElementById('modalPersCantidadInput');
            let cant = (parseInt(input.value) || 1) + delta;
            if (cant < 1) cant = 1;
            if (cant > 99) cant = 99;
            input.value = cant;
            calcularTotalPersonalizado();
        }

        function setCantidadModal(cant) {
            cant = Math.max(1, Math.min(99, parseInt(cant) || 1));
            document.getElementById('modalPersCantidadInput').value = cant;
            calcularTotalPersonalizado();
        }

        function actualizarCantidadModalDesdeInput(val) {
            let cant = parseInt(val);
            if (isNaN(cant) || cant < 1) cant = 1;
            if (cant > 99) cant = 99;
            calcularTotalPersonalizado();
        }

        function calcularTotalPersonalizado() {
            if (!productoEnPersonalizacion) return;
            const inputVal = parseInt(document.getElementById('modalPersCantidadInput').value);
            const cant = isNaN(inputVal) || inputVal < 1 ? 1 : inputVal;
            const extraSabor = saborSeleccionado ? saborSeleccionado.extra : 0;
            const extraExtras = extrasSeleccionados.reduce((acc, curr) => acc + curr.precio, 0);
            const precioUnitario = productoEnPersonalizacion.precio + extraSabor + extraExtras;
            const total = precioUnitario * cant;

            document.getElementById('modalPersTotal').textContent = `$${total.toFixed(2)}`;
            document.getElementById('modalPersLabelCalculo').textContent = `Total (${cant} unidad${cant > 1 ? 'es' : ''}):`;
            document.getElementById('modalPersBtnTexto').textContent = `Agregar ${cant} a la Orden — $${total.toFixed(2)}`;
        }

        function confirmarAgregarPersonalizado() {
            if (!productoEnPersonalizacion) return;
            const inputVal = parseInt(document.getElementById('modalPersCantidadInput').value);
            const cant = isNaN(inputVal) || inputVal < 1 ? 1 : inputVal;
            agregarAlCarrito(productoEnPersonalizacion, saborSeleccionado, extrasSeleccionados, cant);
            modalPersonalizar.hide();
        }

        // Foco automático y confirmación con tecla Enter al digitar cantidad
        document.getElementById('modalPersonalizar').addEventListener('shown.bs.modal', function () {
            const inputCant = document.getElementById('modalPersCantidadInput');
            if (inputCant) {
                inputCant.focus();
                inputCant.select();
            }
        });

        document.getElementById('modalPersCantidadInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmarAgregarPersonalizado();
            }
        });

        // Agregar Ítem al Carrito con soporte de cantidad específica
        function agregarAlCarrito(prod, sabor, extras, cant = 1) {
            cant = Math.max(1, parseInt(cant) || 1);
            const extraSabor = sabor ? sabor.extra : 0;
            const extraExtras = extras && extras.length > 0 ? extras.reduce((acc, curr) => acc + curr.precio, 0) : 0;
            const precioFinal = prod.precio + extraSabor + extraExtras;

            const saborNombre = sabor ? sabor.nombre : null;
            const nombresExtras = extras && extras.length > 0 ? extras.map(e => e.nombre).sort() : [];

            // Clave única para distinguir variantes
            const itemKey = `${prod.id}_${saborNombre || 'base'}_${nombresExtras.join('|')}`;

            const existente = carrito.find(item => item.key === itemKey);
            if (existente) {
                existente.cantidad += cant;
            } else {
                carrito.push({
                    key: itemKey,
                    idProducto: prod.id,
                    nombre: prod.nombre,
                    sabor: saborNombre,
                    extras: nombresExtras,
                    precio: precioFinal,
                    cantidad: cant
                });
            }

            actualizarVistaCarrito();
            renderizarCatalogo();
        }

        // Modificar Cantidad en Carrito (botones +/-)
        function cambiarCantidad(itemKey, delta) {
            const item = carrito.find(i => i.key === itemKey);
            if (!item) return;

            item.cantidad += delta;
            if (item.cantidad <= 0) {
                carrito = carrito.filter(i => i.key !== itemKey);
            }

            actualizarVistaCarrito();
            renderizarCatalogo();
        }

        // Fijar Cantidad Directa desde el input numérico del Carrito
        function fijarCantidadDirecta(itemKey, nuevaCant) {
            const cant = parseInt(nuevaCant);
            if (isNaN(cant) || cant <= 0) {
                eliminarDelCarrito(itemKey);
                return;
            }
            const item = carrito.find(i => i.key === itemKey);
            if (item) {
                item.cantidad = Math.min(99, cant);
                actualizarVistaCarrito();
                renderizarCatalogo();
            }
        }

        // Eliminar Ítem del Carrito
        function eliminarDelCarrito(itemKey) {
            carrito = carrito.filter(i => i.key !== itemKey);
            actualizarVistaCarrito();
            renderizarCatalogo();
        }

        // Vaciar Carrito
        function vaciarOrden() {
            if (carrito.length === 0) return;
            if (confirm('¿Desea vaciar toda la orden actual?')) {
                carrito = [];
                actualizarVistaCarrito();
                renderizarCatalogo();
            }
        }

        // Actualizar Lista del Carrito y Totales con input editable de cantidad
        function actualizarVistaCarrito() {
            listaOrden.innerHTML = '';

            if (carrito.length === 0) {
                listaOrden.appendChild(ordenVaciaMensaje);
                contadorItemsTexto.textContent = '0 productos seleccionados';
                resumenSubtotal.textContent = '$0.00';
                resumenIva.textContent = '$0.00';
                resumenTotal.textContent = '$0.00';
                btnAbrirCobro.disabled = true;
                if (!tieneTurnoActivo) {
                    btnAbrirCobro.innerHTML = `<i class="bi bi-lock-fill me-1"></i> TURNO REQUERIDO`;
                    btnAbrirCobro.title = 'Debe abrir su turno de caja para habilitar el cobro';
                } else {
                    btnAbrirCobro.innerHTML = `<i class="bi bi-credit-card-2-front me-1"></i> COBRAR ORDEN <span class="badge bg-dark text-warning ms-2" style="font-size: 0.75rem; border: 1px solid rgba(245,185,33,0.4);">F2</span>`;
                }
                return;
            }

            let totalGeneral = 0;
            let totalUnidades = 0;

            carrito.forEach(item => {
                const subtotalItem = item.precio * item.cantidad;
                totalGeneral += subtotalItem;
                totalUnidades += item.cantidad;

                const row = document.createElement('div');
                row.className = 'orden-item';
                row.innerHTML = `
                    <div class="orden-item-info">
                        <div class="orden-item-nombre">${item.nombre}</div>
                        ${item.sabor || (item.extras && item.extras.length > 0) ? `
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                ${item.sabor ? `<span class="badge" style="background-color: #333; font-size: 0.68rem; font-weight: 500;">${item.sabor}</span>` : ''}
                                ${item.extras && item.extras.length > 0 ? item.extras.map(e => `<span class="badge" style="background-color: rgba(245, 185, 33, 0.2); color: #f5b921; font-size: 0.68rem; font-weight: 500;">+ ${e}</span>`).join('') : ''}
                            </div>
                        ` : ''}
                        <div class="orden-item-precio-unit mt-1">$${item.precio.toFixed(2)} c/u</div>
                    </div>
                    <div class="orden-item-controles">
                        <button type="button" class="btn-qty" onclick="cambiarCantidad('${item.key}', -1)" title="Restar 1">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" min="1" max="99" class="orden-item-qty-input" value="${item.cantidad}" onchange="fijarCantidadDirecta('${item.key}', this.value)" title="Escribir cantidad directa">
                        <button type="button" class="btn-qty" onclick="cambiarCantidad('${item.key}', 1)" title="Sumar 1">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="orden-item-subtotal">$${subtotalItem.toFixed(2)}</div>
                    <button type="button" class="btn btn-sm text-danger p-0 ms-1" title="Eliminar" onclick="eliminarDelCarrito('${item.key}')">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                `;
                listaOrden.appendChild(row);
            });

            // Cálculos Financieros (IVA 13% incluido)
            const subtotalSinIva = totalGeneral / 1.13;
            const ivaMonto = totalGeneral - subtotalSinIva;

            contadorItemsTexto.textContent = `${totalUnidades} producto(s) en orden`;
            resumenSubtotal.textContent = `$${subtotalSinIva.toFixed(2)}`;
            resumenIva.textContent = `$${ivaMonto.toFixed(2)}`;
            resumenTotal.textContent = `$${totalGeneral.toFixed(2)}`;

            if (!tieneTurnoActivo) {
                btnAbrirCobro.disabled = true;
                btnAbrirCobro.innerHTML = `<i class="bi bi-lock-fill me-1"></i> TURNO REQUERIDO`;
                btnAbrirCobro.title = 'Debe abrir su turno de caja para habilitar el cobro';
            } else {
                btnAbrirCobro.disabled = false;
                btnAbrirCobro.innerHTML = `<i class="bi bi-credit-card-2-front me-1"></i> COBRAR ORDEN <span class="badge bg-dark text-warning ms-2" style="font-size: 0.75rem; border: 1px solid rgba(245,185,33,0.4);">F2</span>`;
            }
        }

        // Manejo de Filtros por Categoría
        document.querySelectorAll('.cat-pill').forEach(pill => {
            pill.addEventListener('click', () => {
                document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                categoriaSeleccionada = pill.dataset.cat;
                renderizarCatalogo();
            });
        });

        // Buscador en tiempo real
        document.getElementById('buscador').addEventListener('input', (e) => {
            terminoBusqueda = e.target.value.trim();
            renderizarCatalogo();
        });

        // Modal de Cobro
        function abrirModalCobro() {
            if (!tieneTurnoActivo) {
                alert('No se puede cobrar sin tener un turno de caja abierto. Redirigiendo a apertura de caja...');
                window.location.href = 'index.php?action=apertura-caja';
                return;
            }

            if (carrito.length === 0) return;

            const totalGeneral = carrito.reduce((acc, curr) => acc + (curr.precio * curr.cantidad), 0);
            const totalUnidades = carrito.reduce((acc, curr) => acc + curr.cantidad, 0);

            document.getElementById('modalMontoTotal').textContent = `$${totalGeneral.toFixed(2)}`;
            document.getElementById('modalCantidadItemsBadge').textContent = `${totalUnidades} producto(s)`;

            // Resetear inputs de cobro
            document.getElementById('inputEfectivoRecibido').value = totalGeneral.toFixed(2);
            seleccionarMetodoPago('efectivo');
            calcularCambio();

            modalCobro.show();
        }

        function seleccionarMetodoPago(metodo) {
            metodoPagoActual = metodo;
            const cardEfectivo = document.getElementById('cardMetodoEfectivo');
            const cardTarjeta = document.getElementById('cardMetodoTarjeta');
            const seccionEfectivo = document.getElementById('seccionEfectivo');
            const seccionTarjeta = document.getElementById('seccionTarjeta');

            if (metodo === 'efectivo') {
                cardEfectivo.classList.add('active');
                cardTarjeta.classList.remove('active');
                seccionEfectivo.classList.remove('d-none');
                seccionTarjeta.classList.add('d-none');
                calcularCambio();
            } else {
                cardTarjeta.classList.add('active');
                cardEfectivo.classList.remove('active');
                seccionTarjeta.classList.remove('d-none');
                seccionEfectivo.classList.add('d-none');
            }
        }

        function setBillete(monto) {
            document.getElementById('inputEfectivoRecibido').value = monto.toFixed(2);
            calcularCambio();
        }

        function setBilleteExacto() {
            const totalGeneral = carrito.reduce((acc, curr) => acc + (curr.precio * curr.cantidad), 0);
            document.getElementById('inputEfectivoRecibido').value = totalGeneral.toFixed(2);
            calcularCambio();
        }

        function calcularCambio() {
            const totalGeneral = carrito.reduce((acc, curr) => acc + (curr.precio * curr.cantidad), 0);
            const recibido = parseFloat(document.getElementById('inputEfectivoRecibido').value) || 0;
            const cambio = recibido - totalGeneral;
            const textoCambio = document.getElementById('textoCambio');
            const boxCambio = document.getElementById('boxCambio');
            const btnConfirmar = document.getElementById('btnConfirmarVenta');

            if (cambio < -0.001) {
                textoCambio.textContent = `-$${Math.abs(cambio).toFixed(2)} (Falta)`;
                textoCambio.className = 'fs-3 fw-bold text-danger';
                boxCambio.style.borderColor = 'var(--cs-rojo)';
                boxCambio.style.backgroundColor = 'rgba(217, 30, 24, 0.12)';
                btnConfirmar.disabled = true;
            } else {
                textoCambio.textContent = `$${Math.max(0, cambio).toFixed(2)}`;
                textoCambio.className = 'fs-3 fw-bold text-success';
                boxCambio.style.borderColor = '#10b981';
                boxCambio.style.backgroundColor = 'rgba(16, 185, 129, 0.12)';
                btnConfirmar.disabled = false;
            }
        }

        // Finalizar Venta y Guardar en BD vía AJAX
        async function finalizarVenta() {
            if (carrito.length === 0) return;

            const totalGeneral = carrito.reduce((acc, curr) => acc + (curr.precio * curr.cantidad), 0);
            const subtotalSinIva = totalGeneral / 1.13;
            const ivaMonto = totalGeneral - subtotalSinIva;
            const recibido = parseFloat(document.getElementById('inputEfectivoRecibido').value) || totalGeneral;
            const cambio = Math.max(0, recibido - totalGeneral);

            const btnConfirmar = document.getElementById('btnConfirmarVenta');
            const btnOriginalText = btnConfirmar.innerHTML;
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando Venta...`;

            const payload = {
                total: parseFloat(totalGeneral.toFixed(2)),
                metodo_pago: metodoPagoActual,
                items: carrito.map(item => ({
                    idProducto: item.idProducto,
                    nombre: item.nombre,
                    sabor: item.sabor,
                    extras: item.extras,
                    precio: parseFloat(item.precio.toFixed(2)),
                    cantidad: parseInt(item.cantidad)
                }))
            };

            try {
                const response = await fetch('index.php?action=procesar-venta-dulceria', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.exito) {
                    throw new Error(data.error || 'Ocurrió un error inesperado al procesar la venta.');
                }

                // Venta registrada en BD exitosamente
                document.getElementById('ticketNumOrden').textContent = data.folio_ticket;
                document.getElementById('ticketCodigoEntrega').textContent = data.codigo_canje;
                document.getElementById('ticketFecha').textContent = data.fecha_hora;
                document.getElementById('ticketSubtotal').textContent = `$${subtotalSinIva.toFixed(2)}`;
                document.getElementById('ticketIva').textContent = `$${ivaMonto.toFixed(2)}`;
                document.getElementById('ticketTotal').textContent = `$${totalGeneral.toFixed(2)}`;
                document.getElementById('ticketMetodo').textContent = data.metodo_pago.toUpperCase();

                if (metodoPagoActual === 'efectivo') {
                    document.getElementById('ticketRowEfectivo').style.display = 'flex';
                    document.getElementById('ticketRecibidoCambio').textContent = `$${recibido.toFixed(2)} / $${cambio.toFixed(2)}`;
                } else {
                    document.getElementById('ticketRowEfectivo').style.display = 'none';
                }

                const tablaItems = document.getElementById('ticketTablaItems');
                tablaItems.innerHTML = '';
                carrito.forEach(item => {
                    const sub = (item.precio * item.cantidad).toFixed(2);
                    let detallesHtml = '';
                    let mods = [];
                    if (item.sabor) mods.push(item.sabor);
                    if (item.extras && item.extras.length > 0) mods.push(...item.extras);
                    if (mods.length > 0) {
                        detallesHtml = `<div style="font-size: 0.64rem; color: #555;">(${mods.join(', ')})</div>`;
                    }

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="padding: 2px 0; vertical-align: top;">${item.cantidad}x</td>
                        <td style="padding: 2px 0; vertical-align: top;">${item.nombre}${detallesHtml}</td>
                        <td style="text-align: right; padding: 2px 0; vertical-align: top;">$${sub}</td>
                    `;
                    tablaItems.appendChild(tr);
                });

                // Cerrar modal de cobro y abrir modal de ticket
                modalCobro.hide();
                modalTicket.show();

            } catch (err) {
                alert('Atención: ' + err.message);
            } finally {
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = btnOriginalText;
            }
        }

        function imprimirTicket() {
            window.print();
        }

        function resetearParaNuevaVenta() {
            carrito = [];
            actualizarVistaCarrito();
            renderizarCatalogo();
            document.getElementById('buscador').value = '';
            terminoBusqueda = '';
        }

        // Atajos de Teclado Profesionales para Mostrador de Alta Velocidad (F2: Cobro, Enter: Confirmar/Imprimir, Esc: Salir, 1-4: Billetes)
        window.addEventListener('keydown', (e) => {
            const isModalCobroOpen = document.getElementById('modalCobro').classList.contains('show');
            const isModalPersOpen = document.getElementById('modalPersonalizar').classList.contains('show');
            const isModalTicketOpen = document.getElementById('modalTicket').classList.contains('show');

            // F2: Abrir modal de cobro
            if (e.key === 'F2') {
                e.preventDefault();
                if (!isModalCobroOpen && !isModalPersOpen && !isModalTicketOpen && carrito.length > 0) {
                    abrirModalCobro();
                }
                return;
            }

            // Enter: Confirmar acción según el modal activo
            if (e.key === 'Enter') {
                if (isModalCobroOpen) {
                    const btnConfirmar = document.getElementById('btnConfirmarVenta');
                    if (!btnConfirmar.disabled) {
                        e.preventDefault();
                        finalizarVenta();
                    }
                } else if (isModalPersOpen) {
                    e.preventDefault();
                    confirmarAgregarPersonalizado();
                } else if (isModalTicketOpen) {
                    e.preventDefault();
                    imprimirTicket();
                }
                return;
            }

            // Escape: Cerrar modal activo o limpiar buscador si está enfocado
            if (e.key === 'Escape') {
                if (isModalTicketOpen) {
                    resetearParaNuevaVenta();
                }
                return;
            }

            // Atajos numéricos directos cuando el modal de cobro en efectivo está abierto
            if (isModalCobroOpen && metodoPagoActual === 'efectivo') {
                // Solo si el usuario no tiene el foco activo en el input numérico
                if (document.activeElement.id !== 'inputEfectivoRecibido') {
                    if (e.key === '1') {
                        e.preventDefault();
                        setBillete(5);
                    } else if (e.key === '2') {
                        e.preventDefault();
                        setBillete(10);
                    } else if (e.key === '3') {
                        e.preventDefault();
                        setBillete(20);
                    } else if (e.key === '4' || e.key === 'e' || e.key === 'E') {
                        e.preventDefault();
                        setBilleteExacto();
                    }
                }
            }
        });

        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', () => {
            renderizarCatalogo();
            actualizarVistaCarrito();
        });
    </script>
</body>
</html>

