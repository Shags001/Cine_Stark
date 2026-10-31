<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var array $salas */
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
    <title>Gestión de Salas | Cine Stark</title>

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
            <h1 class="pos-title">GESTIÓN DE SALAS DE PROYECCIÓN</h1>
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
            <a href="index.php?action=admin-salas" class="pos-pill active"><i class="bi bi-door-open me-1"></i> Salas</a>
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
            <h2 class="section-headline mb-0">SALAS FÍSICAS (AFORO TOTAL: <?= array_sum(array_column($salas, 'capacidad')) ?> BUTACAS)</h2>
            <button type="button" class="btn btn-danger fw-semibold px-3" onclick="abrirModalCrear()">
                <i class="bi bi-plus-circle me-1"></i> Nueva Sala
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

        <div class="row g-3">
            <?php foreach ($salas as $s): ?>
                <div class="col-md-4">
                    <div class="sala-card h-100 p-4" style="max-width: 100%;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge text-bg-secondary mb-2">Sala #<?= (int)$s['id_sala'] ?></span>
                                <h3 class="movie-title mb-1 fs-5"><?= htmlspecialchars($s['nombre_sala']) ?></h3>
                            </div>
                            <i class="bi bi-door-open fs-2 text-danger"></i>
                        </div>
                        <div class="p-3 mb-3 rounded" style="background-color: #1a1a1e; border: 1px solid var(--cs-gris-borde);">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-white-50 small">Capacidad:</span>
                                <span class="text-white fw-bold"><?= (int)$s['capacidad'] ?> butacas</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-white-50 small">Distribución:</span>
                                <span class="text-white-50 small">10 Filas (A-J) x 11 Butacas</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-auto">
                            <button type="button" class="btn btn-sm btn-outline-warning btn-editar-sala"
                                    data-sala="<?= htmlspecialchars(json_encode($s, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                            <a href="index.php?action=admin-eliminar-sala&id=<?= $s['id_sala'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('¿Seguro que desea eliminar esta sala?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal Crear/Editar Sala -->
    <div class="modal fade" id="modalSala" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde); color: var(--cs-blanco);">
                <form action="index.php?action=admin-guardar-sala" method="POST">
                    <input type="hidden" name="id_sala" id="formIdSala" value="0">

                    <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                        <h5 class="modal-title" id="modalTitulo" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px;">NUEVA SALA</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Nombre de la Sala *</label>
                            <input type="text" name="nombre_sala" id="formNombreSala" class="form-control" placeholder="Ej. Sala 1 - Macro" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Capacidad de Butacas *</label>
                            <input type="number" name="capacidad" id="formCapacidad" class="form-control" min="1" max="500" value="110" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-semibold">Guardar Sala</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirModalCrear() {
            document.getElementById('modalTitulo').textContent = 'NUEVA SALA';
            document.getElementById('formIdSala').value = '0';
            document.getElementById('formNombreSala').value = '';
            document.getElementById('formCapacidad').value = '110';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSala')).show();
        }

        function abrirModalEditar(s) {
            document.getElementById('modalTitulo').textContent = 'EDITAR SALA';
            document.getElementById('formIdSala').value = s.id_sala;
            document.getElementById('formNombreSala').value = s.nombre_sala;
            document.getElementById('formCapacidad').value = s.capacidad;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSala')).show();
        }

        // Delegacion de eventos para edicion
        document.querySelectorAll('.btn-editar-sala').forEach(btn => {
            btn.addEventListener('click', function() {
                const sala = JSON.parse(this.dataset.sala);
                abrirModalEditar(sala);
            });
        });
    </script>
</body>
</html>
