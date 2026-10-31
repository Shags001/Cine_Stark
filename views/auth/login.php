<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acceso al sistema de gestión Cine Stark Ticket">
    <title>Iniciar Sesión — Cine Stark</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="public/css/login.css" rel="stylesheet">
</head>
<body class="login-page">

    <!-- Toast de Notificación Superior -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <?php if (!empty($success)): ?>
            <div id="toastFeedback" class="toast toast-stark-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex align-items-center">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="bi bi-patch-check-fill text-dorado fs-5"></i>
                        <div>
                            <div class="fw-bold text-white mb-0">¡Ingreso Exitoso!</div>
                            <small class="toast-subtitle"><?= htmlspecialchars($success) ?></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-brand">
                <img src="img/cinestarklogo.png" alt="Cine Stark Logo" class="brand-logo-img">
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-stark" id="alert-error" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?action=procesar-login" method="POST" autocomplete="off" novalidate>

                <div class="form-group mb-3">
                    <label for="inputUsuario" class="form-label-stark">Usuario</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input
                            type="text"
                            class="form-control stark-input"
                            id="inputUsuario"
                            name="usuario"
                            placeholder="Ingresa tu usuario"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="inputPassword" class="form-label-stark">Contraseña</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input
                            type="password"
                            class="form-control stark-input"
                            id="inputPassword"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            required
                        >
                        <button type="button" class="toggle-password" id="btnTogglePass" aria-label="Mostrar contraseña">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-stark" id="btnLogin">
                    Ingresar
                </button>

            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle contraseña
        document.getElementById('btnTogglePass').addEventListener('click', function () {
            const input = document.getElementById('inputPassword');
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });

        // Inicializar Toast si existe
        const toastEl = document.getElementById('toastFeedback');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, { delay: 6000 });
            toast.show();
        }
    </script>
</body>
</html>
