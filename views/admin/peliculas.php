<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var array $peliculas */
/** @var string|null $mensajeExito */
/** @var string|null $mensajeError */
$nombreAdmin = $nombreAdmin ?? 'Administrador';
$nombreRol = $nombreRol ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Películas | Cine Stark</title>

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
            <h1 class="pos-title">GESTIÓN DE PELÍCULAS</h1>
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
            <a href="index.php?action=admin-peliculas" class="pos-pill active"><i class="bi bi-film me-1"></i> Películas</a>
            <a href="index.php?action=admin-salas" class="pos-pill"><i class="bi bi-door-open me-1"></i> Salas</a>
            <a href="index.php?action=admin-funciones" class="pos-pill"><i class="bi bi-calendar2-week me-1"></i> Cartelera</a>
            <a href="index.php?action=admin-usuarios" class="pos-pill"><i class="bi bi-people me-1"></i> Usuarios</a>
            <a href="index.php?action=admin-cortes" class="pos-pill"><i class="bi bi-cash-stack me-1"></i> Cortes de Caja</a>
            <a href="index.php?action=admin-productos" class="pos-pill"><i class="bi bi-cup-straw me-1"></i> Dulcería</a>
            <a href="index.php?action=admin-reportes" class="pos-pill"><i class="bi bi-file-earmark-bar-graph me-1"></i> Reportes</a>
            <a href="index.php?action=boleteria" class="pos-pill" style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-ticket-perforated me-1"></i> Ir a Boletería</a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="pos-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="section-headline mb-0">CATÁLOGO DE PELÍCULAS</h2>
            <button type="button" class="btn btn-danger fw-semibold px-3" onclick="abrirModalCrear()">
                <i class="bi bi-plus-circle me-1"></i> Nueva Película
            </button>
        </div>

        <?php if (!empty($mensajeExito)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981;">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($mensajeExito) ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensajeError)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: rgba(217, 30, 24, 0.15); border: 1px solid var(--cs-rojo); color: #ff6b6b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($mensajeError) ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="sala-card p-4" style="max-width: 100%;">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--cs-gris-borde); color: var(--cs-blanco-suave);">
                            <th style="width: 80px;">Póster</th>
                            <th>Título</th>
                            <th>Clasificación</th>
                            <th>Duración</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peliculas)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-white-50 py-4">No hay películas registradas en el sistema.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($peliculas as $p): ?>
                                <tr>
                                    <td>
                                        <div style="width: 55px; height: 75px; background-color: #1a1a1e; border: 1px solid var(--cs-gris-borde); border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                            <?php if (!empty($p['imagen']) && file_exists(__DIR__ . '/../../img/' . $p['imagen'])): ?>
                                                <img src="img/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="bi bi-film text-white-50 fs-4"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-white fs-6"><?= htmlspecialchars($p['titulo']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-warning"><?= htmlspecialchars($p['clasificacion']) ?></span>
                                    </td>
                                    <td>
                                        <span class="text-white-50"><?= (int)$p['duracion'] ?> min</span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-warning me-1 btn-editar-pelicula"
                                                data-pelicula="<?= htmlspecialchars(json_encode($p, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>
                                        <a href="index.php?action=admin-eliminar-pelicula&id=<?= $p['id_pelicula'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Está seguro de eliminar esta película?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Crear/Editar Película -->
    <div class="modal fade" id="modalPelicula" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde); color: var(--cs-blanco);">
                <form action="index.php?action=admin-guardar-pelicula" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_pelicula" id="formIdPelicula" value="0">
                    <input type="hidden" name="imagen_actual" id="formImagenActual" value="">

                    <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                        <h5 class="modal-title" id="modalTitulo" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px;">NUEVA PELÍCULA</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Título de la Película *</label>
                            <input type="text" name="titulo" id="formTitulo" class="form-control" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-white-50 small">Clasificación *</label>
                                <select name="clasificacion" id="formClasificacion" class="form-select" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                                    <option value="A">A (Todo público)</option>
                                    <option value="B">B (+12 años)</option>
                                    <option value="B15">B15 (+15 años)</option>
                                    <option value="C">C (Adultos)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white-50 small">Duración (min) *</label>
                                <input type="number" name="duracion" id="formDuracion" class="form-control" min="1" max="400" placeholder="Ej. 120" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Póster / Afiche (Opcional)</label>
                            <input type="file" name="imagen" class="form-control" accept="image/png, image/jpeg, image/webp" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                            <div id="previewImagenNombre" class="form-text text-white-50 small mt-1"></div>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-semibold">Guardar Película</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirModalCrear() {
            document.getElementById('modalTitulo').textContent = 'NUEVA PELÍCULA';
            document.getElementById('formIdPelicula').value = '0';
            document.getElementById('formImagenActual').value = '';
            document.getElementById('formTitulo').value = '';
            document.getElementById('formClasificacion').value = 'B';
            document.getElementById('formDuracion').value = '';
            document.getElementById('previewImagenNombre').textContent = '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPelicula')).show();
        }

        function abrirModalEditar(p) {
            document.getElementById('modalTitulo').textContent = 'EDITAR PELÍCULA';
            document.getElementById('formIdPelicula').value = p.id_pelicula;
            document.getElementById('formImagenActual').value = p.imagen || '';
            document.getElementById('formTitulo').value = p.titulo;
            document.getElementById('formClasificacion').value = p.clasificacion;
            document.getElementById('formDuracion').value = p.duracion;
            document.getElementById('previewImagenNombre').textContent = p.imagen ? ('Imagen actual: ' + p.imagen) : 'Sin imagen cargada';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPelicula')).show();
        }

        // Delegacion de eventos para edicion
        document.querySelectorAll('.btn-editar-pelicula').forEach(btn => {
            btn.addEventListener('click', function() {
                const pelicula = JSON.parse(this.dataset.pelicula);
                abrirModalEditar(pelicula);
            });
        });
    </script>
</body>
</html>
