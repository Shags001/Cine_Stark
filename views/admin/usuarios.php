<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var int $idAdminActual */
/** @var array $usuarios */
/** @var array $roles */
/** @var string|null $mensajeExito */
/** @var string|null $mensajeError */
$nombreAdmin = $nombreAdmin ?? 'Administrador';
$nombreRol = $nombreRol ?? 'Administrador';
$idAdminActual = $idAdminActual ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | Cine Stark</title>

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
            <h1 class="pos-title">CONTROL DE USUARIOS Y ACCESOS</h1>
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
            <a href="index.php?action=admin-usuarios" class="pos-pill active"><i class="bi bi-people me-1"></i> Usuarios</a>
            <a href="index.php?action=admin-cortes" class="pos-pill"><i class="bi bi-cash-stack me-1"></i> Cortes de Caja</a>
            <a href="index.php?action=admin-productos" class="pos-pill"><i class="bi bi-cup-straw me-1"></i> Dulcería</a>
            <a href="index.php?action=admin-reportes" class="pos-pill"><i class="bi bi-file-earmark-bar-graph me-1"></i> Reportes</a>
            <a href="index.php?action=boleteria" class="pos-pill" style="border-color: var(--cs-dorado); color: var(--cs-dorado);"><i class="bi bi-ticket-perforated me-1"></i> Ir a Boletería</a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="pos-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="section-headline mb-0">USUARIOS DEL SISTEMA</h2>
            <button type="button" class="btn btn-danger fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="abrirModalCrear()">
                <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
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
                            <th>Nombre Completo</th>
                            <th>Usuario</th>
                            <th>Rol Asignado</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <?php $esActivo = ($u['estado'] === 'Activo' || (int)$u['estado'] === 1); ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 34px; height: 34px; background-color: #1a1a1e; border: 1px solid var(--cs-gris-borde); color: var(--cs-dorado);">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <span class="fw-bold text-white"><?= htmlspecialchars($u['nombre']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <code class="text-warning-emphasis fw-semibold"><?= htmlspecialchars($u['usuario']) ?></code>
                                </td>
                                <td>
                                    <?php if ((int)$u['id_rol'] === 1): ?>
                                        <span class="badge text-bg-danger"><?= htmlspecialchars($u['nombre_rol']) ?></span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary"><?= htmlspecialchars($u['nombre_rol']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($esActivo): ?>
                                        <span class="badge text-bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-dark text-white-50 border border-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-warning me-1"
                                            onclick='abrirModalEditar(<?= json_encode($u, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </button>

                                    <?php if ((int)$u['id_usuario'] !== $idAdminActual): ?>
                                        <a href="index.php?action=admin-toggle-usuario&id=<?= $u['id_usuario'] ?>"
                                           class="btn btn-sm <?= $esActivo ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                           title="<?= $esActivo ? 'Desactivar usuario' : 'Activar usuario' ?>">
                                            <i class="bi <?= $esActivo ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                            <?= $esActivo ? 'Desactivar' : 'Activar' ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Crear/Editar Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde); color: var(--cs-blanco);">
                <form action="index.php?action=admin-guardar-usuario" method="POST">
                    <input type="hidden" name="id_usuario" id="formIdUsuario" value="0">

                    <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                        <h5 class="modal-title" id="modalTitulo" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px;">NUEVO USUARIO</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Nombre Completo *</label>
                            <input type="text" name="nombre" id="formNombre" class="form-control" placeholder="Ej. Juan Pérez" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Nombre de Usuario (Login) *</label>
                            <input type="text" name="usuario" id="formUsuario" class="form-control" placeholder="Ej. juanp" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small" id="lblPassword">Contraseña *</label>
                            <input type="password" name="password" id="formPassword" class="form-control" placeholder="Ingrese contraseña" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                            <div class="form-text text-white-50 small" id="helpPassword">Obligatoria para usuarios nuevos.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Rol en el Sistema *</label>
                            <select name="id_rol" id="formIdRol" class="form-select" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id_rol'] ?>"><?= htmlspecialchars($r['nombre_rol']) ?> — <?= htmlspecialchars($r['descripcion']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-semibold">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalUsuarioEl = document.getElementById('modalUsuario');
        const modalUsuario = new bootstrap.Modal(modalUsuarioEl);

        function abrirModalCrear() {
            document.getElementById('modalTitulo').textContent = 'NUEVO USUARIO';
            document.getElementById('formIdUsuario').value = '0';
            document.getElementById('formNombre').value = '';
            document.getElementById('formUsuario').value = '';
            document.getElementById('formPassword').value = '';
            document.getElementById('formPassword').required = true;
            document.getElementById('lblPassword').textContent = 'Contraseña *';
            document.getElementById('helpPassword').textContent = 'Obligatoria para usuarios nuevos.';
            document.getElementById('formIdRol').value = '2'; // Cajero por defecto
        }

        function abrirModalEditar(u) {
            document.getElementById('modalTitulo').textContent = 'EDITAR USUARIO';
            document.getElementById('formIdUsuario').value = u.id_usuario;
            document.getElementById('formNombre').value = u.nombre;
            document.getElementById('formUsuario').value = u.usuario;
            document.getElementById('formPassword').value = '';
            document.getElementById('formPassword').required = false;
            document.getElementById('lblPassword').textContent = 'Nueva Contraseña (Opcional)';
            document.getElementById('helpPassword').textContent = 'Deje en blanco si no desea cambiarla.';
            document.getElementById('formIdRol').value = u.id_rol;
            modalUsuario.show();
        }
    </script>
</body>
</html>
