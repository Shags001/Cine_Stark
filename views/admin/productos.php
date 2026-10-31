<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var array $productos */
/** @var array $categorias */
/** @var int $totalProductos */
/** @var int $totalStock */
/** @var int $totalAgotados */
/** @var string|null $mensajeExito */
/** @var string|null $mensajeError */
$nombreAdmin = $nombreAdmin ?? 'Administrador';
$nombreRol = $nombreRol ?? 'Administrador';
$productos = $productos ?? [];
$categorias = $categorias ?? [];
$categoriaActual = $_GET['categoria'] ?? 'todos';
$busquedaActual = $_GET['buscar'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Dulcería y Productos | Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/boleteria.css" rel="stylesheet">
    <link href="public/css/admin-productos.css" rel="stylesheet">
</head>

<body class="pos-body">

    <!-- Header Principal -->
    <header class="pos-header">
        <div class="pos-brand">
            <img src="img/cinestarklogo.png" alt="Cine Stark" class="pos-logo-img">
            <h1 class="pos-title">GESTIÓN DE DULCERÍA Y PRODUCTOS</h1>
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
            <a href="index.php?action=admin-dashboard" class="pos-pill"><i class="bi bi-speedometer2 me-1"></i>
                Resumen</a>
            <a href="index.php?action=admin-peliculas" class="pos-pill"><i class="bi bi-film me-1"></i> Películas</a>
            <a href="index.php?action=admin-salas" class="pos-pill"><i class="bi bi-door-open me-1"></i> Salas</a>
            <a href="index.php?action=admin-funciones" class="pos-pill"><i class="bi bi-calendar2-week me-1"></i>
                Cartelera</a>
            <a href="index.php?action=admin-usuarios" class="pos-pill"><i class="bi bi-people me-1"></i> Usuarios</a>
            <a href="index.php?action=admin-cortes" class="pos-pill"><i class="bi bi-cash-stack me-1"></i> Cortes de
                Caja</a>
            <a href="index.php?action=admin-productos" class="pos-pill active"><i class="bi bi-cup-straw me-1"></i>
                Dulcería</a>
            <a href="index.php?action=admin-reportes" class="pos-pill"><i class="bi bi-printer me-1"></i> Reportes</a>
            <a href="index.php?action=dulceria" class="pos-pill"
                style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-shop me-1"></i> Ir al
                POS</a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="pos-content">

        <!-- Mensajes de Notificación -->
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

        <!-- Tarjetas de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="metric-card-admin">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-semibold">Productos Registrados</span>
                        <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= count($productos) ?></h3>
                        <small class="text-white-50">En el catálogo actual</small>
                    </div>
                    <div class="rounded-circle p-3"
                        style="background-color: rgba(245, 185, 33, 0.15); color: var(--cs-dorado);">
                        <i class="bi bi-box-seam fs-2"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="metric-card-admin">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-semibold">Unidades en Inventario</span>
                        <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= $totalStock ?></h3>
                        <small class="text-white-50">Stock físico total</small>
                    </div>
                    <div class="rounded-circle p-3" style="background-color: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                        <i class="bi bi-layers-half fs-2"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="metric-card-admin">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-semibold">Productos Agotados</span>
                        <h3 class="fs-1 fw-bold <?= $totalAgotados > 0 ? 'text-danger' : 'text-success' ?> mb-0 mt-1">
                            <?= $totalAgotados ?></h3>
                        <small
                            class="text-white-50"><?= $totalAgotados > 0 ? 'Requieren reposición' : 'Todo en orden' ?></small>
                    </div>
                    <div class="rounded-circle p-3"
                        style="background-color: <?= $totalAgotados > 0 ? 'rgba(217, 30, 24, 0.15)' : 'rgba(16, 185, 129, 0.15)' ?>; color: <?= $totalAgotados > 0 ? 'var(--cs-rojo)' : '#34d399' ?>;">
                        <i class="bi bi-exclamation-octagon fs-2"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="metric-card-admin">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-semibold">Líneas de Menú</span>
                        <h3 class="fs-1 fw-bold text-white mb-0 mt-1"><?= count($categorias) ?></h3>
                        <small class="text-white-50">Combos, Snacks, Bebidas...</small>
                    </div>
                    <div class="rounded-circle p-3" style="background-color: rgba(168, 85, 247, 0.15); color: #c084fc;">
                        <i class="bi bi-tags fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Encabezado de la Sección con Buscador y Filtros -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="section-headline mb-0">CATÁLOGO DE PRODUCTOS Y COMBOS</h2>
                <p class="text-white-50 small mb-0">Administre precios, imágenes comerciales, modificadores y niveles de
                    stock.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?action=admin-reportes&tipo=inventario"
                    class="btn btn-outline-light fw-semibold px-3 py-2">
                    <i class="bi bi-printer me-1"></i> Imprimir Inventario
                </a>
                <button type="button" class="btn btn-danger fw-semibold px-3 py-2" onclick="abrirModalCrear()">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
                </button>
            </div>
        </div>

        <!-- Barra de Filtros y Búsqueda -->
        <div class="sala-card p-3 mb-4" style="max-width: 100%;">
            <form action="index.php" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="action" value="admin-productos">

                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 translate-middle-y text-white-50"
                            style="left: 14px;"></i>
                        <input type="text" name="buscar" class="form-control ps-5"
                            placeholder="Buscar por nombre o descripción..."
                            value="<?= htmlspecialchars($busquedaActual) ?>"
                            style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-2 overflow-x-auto pb-1">
                        <a href="index.php?action=admin-productos&categoria=todos<?= $busquedaActual !== '' ? '&buscar=' . urlencode($busquedaActual) : '' ?>"
                            class="cat-filter-btn <?= $categoriaActual === 'todos' ? 'active' : '' ?>">
                            <i class="bi bi-grid-fill"></i> Todos
                        </a>
                        <a href="index.php?action=admin-productos&categoria=combos<?= $busquedaActual !== '' ? '&buscar=' . urlencode($busquedaActual) : '' ?>"
                            class="cat-filter-btn <?= $categoriaActual === 'combos' ? 'active' : '' ?>">
                            <i class="bi bi-stars text-warning"></i> Combos
                        </a>
                        <a href="index.php?action=admin-productos&categoria=palomitas<?= $busquedaActual !== '' ? '&buscar=' . urlencode($busquedaActual) : '' ?>"
                            class="cat-filter-btn <?= $categoriaActual === 'palomitas' ? 'active' : '' ?>">
                            <i class="bi bi-box2"></i> Palomitas
                        </a>
                        <a href="index.php?action=admin-productos&categoria=bebidas<?= $busquedaActual !== '' ? '&buscar=' . urlencode($busquedaActual) : '' ?>"
                            class="cat-filter-btn <?= $categoriaActual === 'bebidas' ? 'active' : '' ?>">
                            <i class="bi bi-cup-straw"></i> Bebidas
                        </a>
                        <a href="index.php?action=admin-productos&categoria=snacks<?= $busquedaActual !== '' ? '&buscar=' . urlencode($busquedaActual) : '' ?>"
                            class="cat-filter-btn <?= $categoriaActual === 'snacks' ? 'active' : '' ?>">
                            <i class="bi bi-fire"></i> Snacks
                        </a>
                        <a href="index.php?action=admin-productos&categoria=dulces<?= $busquedaActual !== '' ? '&buscar=' . urlencode($busquedaActual) : '' ?>"
                            class="cat-filter-btn <?= $categoriaActual === 'dulces' ? 'active' : '' ?>">
                            <i class="bi bi-bag-heart"></i> Golosinas
                        </a>
                    </div>
                </div>

                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-outline-warning w-100">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Productos -->
        <div class="sala-card p-4" style="max-width: 100%;">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--cs-gris-borde); color: var(--cs-blanco-suave);">
                            <th style="width: 70px;">Foto</th>
                            <th>Producto / Descripción</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Tipo</th>
                            <th>Ventas</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-white-50 py-5">
                                <i class="bi bi-cup-straw display-4 d-block mb-2 text-white-50"
                                    style="opacity: 0.3;"></i>
                                No se encontraron productos que coincidan con los criterios seleccionados.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($productos as $p): ?>
                        <?php
                                    $catClass = 'badge-cat-' . strtolower(trim($p['categoria']));
                                    $tieneFoto = !empty($p['imagen']) && file_exists(__DIR__ . '/../../' . $p['imagen']);
                                    $stock = (int)$p['stock_disponible'];
                                    $esActivo = (int)$p['activo'] === 1;
                                ?>
                        <tr style="border-color: #2b2b34;">
                            <td>
                                <div class="prod-thumb-box">
                                    <?php if ($tieneFoto): ?>
                                    <img src="<?= htmlspecialchars($p['imagen']) ?>"
                                        alt="<?= htmlspecialchars($p['nombre']) ?>" class="prod-thumb-img">
                                    <?php else: ?>
                                    <i class="bi bi-image text-white-50 fs-4"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong class="text-white d-block"
                                    style="font-size: 0.95rem;"><?= htmlspecialchars($p['nombre']) ?></strong>
                                <small class="text-white-50"
                                    style="font-size: 0.78rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción') ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge <?= $catClass ?> text-uppercase"
                                    style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($p['categoria']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dorado fs-5"
                                    style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 0.5px;">
                                    $ <?= number_format($p['precio'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($stock > 10): ?>
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check2-circle me-1"></i> <?= $stock ?> uds.
                                </span>
                                <?php elseif ($stock > 0): ?>
                                <span
                                    class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-exclamation-circle me-1"></i> <?= $stock ?> uds. (Bajo)
                                </span>
                                <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i> Agotado
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$p['personalizable'] === 1): ?>
                                <span class="badge"
                                    style="background-color: rgba(245, 185, 33, 0.15); color: #f5b921; border: 1px solid rgba(245, 185, 33, 0.3);">
                                    <i class="bi bi-sliders me-1"></i> Sabores/Extras
                                </span>
                                <?php else: ?>
                                <span class="badge" style="background-color: #2b2b34; color: #bbb;">
                                    Estándar
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small text-white-50 fw-semibold">
                                    <?= (int)($p['total_vendidos'] ?? 0) ?> ventas
                                </span>
                            </td>
                            <td>
                                <a href="index.php?action=admin-toggle-producto&id=<?= $p['id_producto'] ?>"
                                    class="text-decoration-none" title="Haz clic para alternar estado">
                                    <?php if ($esActivo): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Activo</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-eye-slash me-1"></i>
                                        Inactivo</span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Editar Producto"
                                        onclick='abrirModalEditar(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        title="Eliminar Producto"
                                        onclick="confirmarEliminar(<?= $p['id_producto'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Crear / Editar Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"
                style="background-color: var(--cs-negro); border: 1px solid var(--cs-gris-borde); color: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-cup-straw fs-4 text-warning"></i>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalProductoTitulo">NUEVO PRODUCTO</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="index.php?action=admin-guardar-producto" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_producto" id="inputIdProducto" value="0">
                    <input type="hidden" name="imagen_actual" id="inputImagenActual" value="">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <!-- Nombre del Producto -->
                            <div class="col-md-7">
                                <label for="inputNombre" class="form-label text-white-50 small fw-semibold">Nombre
                                    comercial del producto *</label>
                                <input type="text" name="nombre" id="inputNombre" class="form-control"
                                    placeholder="Ej. Combo Mega Stark, Popcorn Caramelo..." required
                                    style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-5">
                                <label for="selectCategoria"
                                    class="form-label text-white-50 small fw-semibold">Categoría *</label>
                                <select name="categoria" id="selectCategoria" class="form-select" required
                                    style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                                    <option value="combos">Combos Especiales</option>
                                    <option value="palomitas">Palomitas / Popcorn</option>
                                    <option value="bebidas">Bebidas y Refrescos</option>
                                    <option value="snacks">Snacks Calientes</option>
                                    <option value="dulces">Dulces y Golosinas</option>
                                </select>
                            </div>

                            <!-- Precio y Stock -->
                            <div class="col-md-4">
                                <label for="inputPrecio" class="form-label text-white-50 small fw-semibold">Precio de
                                    venta ($) *</label>
                                <div class="input-group">
                                    <span class="input-group-text"
                                        style="background-color: #2b2b34; border-color: var(--cs-gris-borde); color: var(--cs-dorado);">$</span>
                                    <input type="number" step="0.01" min="0.05" name="precio" id="inputPrecio"
                                        class="form-control" placeholder="0.00" required
                                        style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="inputStock" class="form-label text-white-50 small fw-semibold">Stock
                                    disponible (unidades)</label>
                                <input type="number" min="0" name="stock_disponible" id="inputStock"
                                    class="form-control" value="100"
                                    style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                            </div>

                            <div class="col-md-4 d-flex align-items-center pt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="personalizable"
                                        id="switchPersonalizable" value="1" checked
                                        style="background-color: #333; border-color: #555;">
                                    <label class="form-check-label text-white small" for="switchPersonalizable">
                                        ¿Lleva sabores o extras?
                                    </label>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="col-12">
                                <label for="inputDescripcion"
                                    class="form-label text-white-50 small fw-semibold">Descripción o contenido del
                                    producto</label>
                                <textarea name="descripcion" id="inputDescripcion" rows="2" class="form-control"
                                    placeholder="Ej. 1 Popcorn grande + 2 refrescos medianos + 1 golosina..."
                                    style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;"></textarea>
                            </div>

                            <!-- Selección y Subida de Imagen -->
                            <div class="col-12">
                                <hr style="border-color: var(--cs-gris-borde);">
                                <h6 class="fw-bold text-warning mb-2"><i class="bi bi-image me-1"></i> FOTOGRAFÍA DEL
                                    PRODUCTO</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-semibold">Subir nueva foto desde tu
                                    equipo (JPG, PNG, WEBP)</label>
                                <input type="file" name="imagen" id="inputFileImagen" class="form-control"
                                    accept="image/*"
                                    style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;"
                                    onchange="previsualizarArchivo(this)">
                                <small class="text-white-50 d-block mt-1">Recomendado relación 4:3 o cuadrada con fondo
                                    oscuro.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white-50 small fw-semibold">O reutilizar foto oficial
                                    existente</label>
                                <select name="imagen_sugerida" id="selectImagenSugerida" class="form-select"
                                    style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;"
                                    onchange="seleccionarImagenPredefinida(this.value)">
                                    <option value="">-- Seleccionar imagen del catálogo CineStark --</option>
                                    <option value="img/dulceria/palomitas.jpg">Palomitas / Popcorn Oficial
                                        (palomitas.jpg)</option>
                                    <option value="img/dulceria/combo_stark.jpg">Combo Stark Duo (combo_stark.jpg)
                                    </option>
                                    <option value="img/dulceria/combo_pareja.jpg">Combo Pareja Mega (combo_pareja.jpg)
                                    </option>
                                    <option value="img/dulceria/combo_individual.jpg">Combo Individual
                                        (combo_individual.jpg)</option>
                                    <option value="img/dulceria/combo_infantil.jpg">Combo Infantil (combo_infantil.jpg)
                                    </option>
                                    <option value="img/dulceria/combo_hotdog.jpg">Combo Hot Dog (combo_hotdog.jpg)
                                    </option>
                                    <option value="img/dulceria/combo_nachos.jpg">Combo Nachos (combo_nachos.jpg)
                                    </option>
                                    <option value="img/dulceria/refresco.jpg">Refresco Gaseosa (refresco.jpg)</option>
                                    <option value="img/dulceria/icee.jpg">Icee Granizado Helado (icee.jpg)</option>
                                    <option value="img/dulceria/agua.jpg">Agua Embotellada (agua.jpg)</option>
                                    <option value="img/dulceria/te_frio.jpg">Té Frío con Limón (te_frio.jpg)</option>
                                    <option value="img/dulceria/nachos.jpg">Nachos con Queso (nachos.jpg)</option>
                                    <option value="img/dulceria/hotdog.jpg">Hot Dog Clásico (hotdog.jpg)</option>
                                    <option value="img/dulceria/mms_mani.jpg">M&Ms Maní Pack Cine (mms_mani.jpg)
                                    </option>
                                    <option value="img/dulceria/skittles.jpg">Skittles Frutales Original (skittles.jpg)
                                    </option>
                                    <option value="img/dulceria/snickers.jpg">Snickers King Size (snickers.jpg)</option>
                                    <option value="img/dulceria/sour_patch.jpg">Gomitas Ácidas Sour Patch
                                        (sour_patch.jpg)</option>
                                </select>
                            </div>

                            <!-- Vista Previa de Imagen -->
                            <div class="col-12 text-center mt-3">
                                <div class="p-2 rounded d-inline-block"
                                    style="background-color: #111; border: 1px dashed var(--cs-gris-borde);">
                                    <img id="imgPreviewModal" src="img/cinestarklogo.png" alt="Vista Previa"
                                        style="max-height: 120px; max-width: 200px; object-fit: contain; border-radius: 6px;">
                                </div>
                            </div>

                            <!-- Estado Activo -->
                            <div class="col-12 pt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="activo" id="switchActivo"
                                        value="1" checked style="background-color: #333; border-color: #555;">
                                    <label class="form-check-label text-white small" for="switchActivo">
                                        Producto disponible y activo para la venta en el mostrador POS
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-semibold px-4" id="btnGuardarProducto">
                            <i class="bi bi-save me-1"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="background-color: var(--cs-negro); border: 1px solid var(--cs-gris-borde); color: #fff;">
                <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="index.php?action=admin-eliminar-producto" method="POST">
                    <input type="hidden" name="id_producto" id="inputEliminarId" value="0">
                    <div class="modal-body py-4">
                        <p class="mb-2">¿Está seguro de que desea eliminar el producto <strong id="textoEliminarNombre"
                                class="text-white"></strong>?</p>
                        <div class="alert alert-warning py-2 mb-0 small"
                            style="background-color: rgba(245, 185, 33, 0.1); border-color: rgba(245, 185, 33, 0.3); color: #f5b921;">
                            <i class="bi bi-shield-check me-1"></i> Si el producto ya registra ventas en auditoría, el
                            sistema lo desactivará de forma segura sin borrar el historial contable.
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-semibold">
                            <i class="bi bi-trash-fill me-1"></i> Sí, Proceder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));

    function abrirModalCrear() {
        document.getElementById('modalProductoTitulo').textContent = 'NUEVO PRODUCTO';
        document.getElementById('inputIdProducto').value = '0';
        document.getElementById('inputNombre').value = '';
        document.getElementById('selectCategoria').value = 'palomitas';
        document.getElementById('inputPrecio').value = '';
        document.getElementById('inputStock').value = '100';
        document.getElementById('inputDescripcion').value = '';
        document.getElementById('switchPersonalizable').checked = true;
        document.getElementById('switchActivo').checked = true;
        document.getElementById('inputImagenActual').value = '';
        document.getElementById('inputFileImagen').value = '';
        document.getElementById('selectImagenSugerida').value = '';
        document.getElementById('imgPreviewModal').src = 'img/cinestarklogo.png';
        modalProducto.show();
    }

    function abrirModalEditar(p) {
        document.getElementById('modalProductoTitulo').textContent = 'EDITAR PRODUCTO #' + p.id_producto;
        document.getElementById('inputIdProducto').value = p.id_producto;
        document.getElementById('inputNombre').value = p.nombre || '';
        document.getElementById('selectCategoria').value = p.categoria || 'palomitas';
        document.getElementById('inputPrecio').value = parseFloat(p.precio || 0).toFixed(2);
        document.getElementById('inputStock').value = p.stock_disponible || 0;
        document.getElementById('inputDescripcion').value = p.descripcion || '';
        document.getElementById('switchPersonalizable').checked = (parseInt(p.personalizable) === 1);
        document.getElementById('switchActivo').checked = (parseInt(p.activo) === 1);
        document.getElementById('inputImagenActual').value = p.imagen || '';
        document.getElementById('inputFileImagen').value = '';
        document.getElementById('selectImagenSugerida').value = p.imagen || '';

        if (p.imagen) {
            document.getElementById('imgPreviewModal').src = p.imagen;
        } else {
            document.getElementById('imgPreviewModal').src = 'img/cinestarklogo.png';
        }

        modalProducto.show();
    }

    function confirmarEliminar(id, nombre) {
        document.getElementById('inputEliminarId').value = id;
        document.getElementById('textoEliminarNombre').textContent = nombre;
        modalEliminar.show();
    }

    function seleccionarImagenPredefinida(ruta) {
        if (ruta) {
            document.getElementById('imgPreviewModal').src = ruta;
            document.getElementById('inputImagenActual').value = ruta;
            document.getElementById('inputFileImagen').value = '';
        }
    }

    function previsualizarArchivo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreviewModal').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>

</html>