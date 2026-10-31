<?php
/** @var string $nombreAdmin */
/** @var string $nombreRol */
/** @var array $funciones */
/** @var array $peliculas */
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
    <title>Gestión de Cartelera y Horarios | Cine Stark</title>

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
            <h1 class="pos-title">PROGRAMACIÓN DE CARTELERA Y HORARIOS</h1>
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
            <a href="index.php?action=admin-funciones" class="pos-pill active"><i class="bi bi-calendar2-week me-1"></i> Cartelera</a>
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
            <h2 class="section-headline mb-0">FUNCIONES PROGRAMADAS</h2>
            <button type="button" class="btn btn-danger fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalFuncion" onclick="abrirModalCrear()">
                <i class="bi bi-plus-circle me-1"></i> Programar Función
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
                            <th>Película</th>
                            <th>Sala</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Formato</th>
                            <th>Precio Boleto</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($funciones)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-white-50 py-4">No hay funciones registradas en cartelera.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($funciones as $f): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-film text-danger"></i>
                                            <span class="fw-bold text-white"><?= htmlspecialchars($f['pelicula_titulo']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($f['nombre_sala']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($f['fecha_formato']) ?></td>
                                    <td>
                                        <span class="badge text-bg-dark border border-secondary"><?= htmlspecialchars($f['hora_formato']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-warning"><?= htmlspecialchars($f['formato']) ?></span>
                                    </td>
                                    <td class="fw-bold text-success">$<?= number_format((float)$f['precio_boleto'], 2) ?></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-warning me-1"
                                                onclick='abrirModalEditar(<?= json_encode($f, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>
                                        <a href="index.php?action=admin-eliminar-funcion&id=<?= $f['id_funcion'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Está seguro de eliminar esta función de la cartelera?');">
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

    <!-- Modal Crear/Editar Función -->
    <div class="modal fade" id="modalFuncion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--cs-gris-carbon); border: 1px solid var(--cs-gris-borde); color: var(--cs-blanco);">
                <form action="index.php?action=admin-guardar-funcion" method="POST">
                    <input type="hidden" name="id_funcion" id="formIdFuncion" value="0">

                    <div class="modal-header" style="border-bottom: 1px solid var(--cs-gris-borde);">
                        <h5 class="modal-title" id="modalTitulo" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px;">PROGRAMAR FUNCIÓN</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Película *</label>
                            <select name="id_pelicula" id="formIdPelicula" class="form-select" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                                <option value="">-- Seleccionar Película --</option>
                                <?php foreach ($peliculas as $p): ?>
                                    <option value="<?= $p['id_pelicula'] ?>" data-duracion="<?= (int)$p['duracion'] ?>"><?= htmlspecialchars($p['titulo']) ?> (<?= $p['clasificacion'] ?> · <?= (int)$p['duracion'] ?> min)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Sala Asignada *</label>
                            <select name="id_sala" id="formIdSala" class="form-select" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                                <option value="">-- Seleccionar Sala --</option>
                                <?php foreach ($salas as $s): ?>
                                    <option value="<?= $s['id_sala'] ?>"><?= htmlspecialchars($s['nombre_sala']) ?> (<?= $s['capacidad'] ?> butacas)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small">Fecha de Proyección *</label>
                            <input type="date" name="fecha" id="formFecha" class="form-control" value="<?= date('Y-m-d') ?>" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                        </div>

                        <!-- Horarios multiples en creacion -->
                        <div class="mb-3" id="seccionHorariosCrear">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label text-white-50 small mb-0">Horarios / Tandas del Día *</label>
                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2" onclick="agregarCampoHora()" style="font-size: 0.78rem;">
                                    <i class="bi bi-plus-lg"></i> Agregar Horario
                                </button>
                            </div>
                            <div id="contenedorHoras" class="d-flex flex-column gap-2">
                                <div class="input-group">
                                    <input type="time" name="horas[]" class="form-control input-hora-dinamica" value="14:00" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                                    <button type="button" class="btn btn-outline-secondary" disabled style="opacity: 0.5;"><i class="bi bi-dash"></i></button>
                                </div>
                            </div>
                            <span class="text-white-50 small" style="font-size: 0.75rem;">Podés agregar todas las tandas del día para esta película y sala. El sistema valida automáticamente que no se solapen con otras proyecciones ni entre sí según la duración.</span>
                        </div>

                        <!-- Horario individual en edicion -->
                        <div class="mb-3 d-none" id="seccionHorarioEditar">
                            <label class="form-label text-white-50 small">Horario / Tanda *</label>
                            <input type="time" name="hora" id="formHoraEditar" class="form-control" value="14:00" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-white-50 small">Precio Boleto ($) *</label>
                                <input type="number" step="0.25" min="1" name="precio_boleto" id="formPrecio" class="form-control" value="4.00" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white-50 small">Formato *</label>
                                <select name="formato" id="formFormato" class="form-select" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                                    <option value="2D">2D Digital</option>
                                    <option value="3D">3D Real</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid var(--cs-gris-borde);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-semibold">Guardar Funciones</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalFuncionEl = document.getElementById('modalFuncion');
        const modalFuncion = new bootstrap.Modal(modalFuncionEl);
        const contenedorHoras = document.getElementById('contenedorHoras');

        function agregarCampoHora(valor = '') {
            const div = document.createElement('div');
            div.className = 'input-group';
            div.innerHTML = `
                <input type="time" name="horas[]" class="form-control input-hora-dinamica" value="${valor || '16:30'}" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()" title="Quitar horario"><i class="bi bi-trash"></i></button>
            `;
            contenedorHoras.appendChild(div);
        }

        function abrirModalCrear() {
            document.getElementById('modalTitulo').textContent = 'PROGRAMAR FUNCIONES';
            document.getElementById('formIdFuncion').value = '0';
            document.getElementById('formIdPelicula').value = '';
            document.getElementById('formIdSala').value = '';
            document.getElementById('formFecha').value = new Date().toISOString().split('T')[0];
            document.getElementById('formPrecio').value = '4.00';
            document.getElementById('formFormato').value = '2D';

            // Mostrar sección de múltiples horarios
            document.getElementById('seccionHorariosCrear').classList.remove('d-none');
            document.getElementById('seccionHorarioEditar').classList.add('d-none');
            document.getElementById('formHoraEditar').removeAttribute('required');

            // Resetear lista de horarios a 3 por defecto
            contenedorHoras.innerHTML = `
                <div class="input-group">
                    <input type="time" name="horas[]" class="form-control input-hora-dinamica" value="14:00" style="background-color: #1a1a1e; border-color: var(--cs-gris-borde); color: #fff;" required>
                    <button type="button" class="btn btn-outline-secondary" disabled style="opacity: 0.5;"><i class="bi bi-dash"></i></button>
                </div>
            `;
            agregarCampoHora('16:30');
            agregarCampoHora('19:00');
        }

        function abrirModalEditar(f) {
            document.getElementById('modalTitulo').textContent = 'EDITAR FUNCIÓN';
            document.getElementById('formIdFuncion').value = f.id_funcion;
            document.getElementById('formIdPelicula').value = f.id_pelicula;
            document.getElementById('formIdSala').value = f.id_sala;
            
            const partes = f.fecha_hora.split(' ');
            document.getElementById('formFecha').value = partes[0] || '';

            // En modo edición solo se edita la tanda específica
            document.getElementById('seccionHorariosCrear').classList.add('d-none');
            document.getElementById('seccionHorarioEditar').classList.remove('d-none');
            document.getElementById('formHoraEditar').value = (partes[1] || '00:00').substring(0, 5);
            document.getElementById('formHoraEditar').setAttribute('required', 'true');
            
            document.getElementById('formPrecio').value = f.precio_boleto;
            document.getElementById('formFormato').value = f.formato;
            modalFuncion.show();
        }
    </script>
</body>
</html>
