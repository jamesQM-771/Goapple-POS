<?php
/**
 * Registro de Usuario
 */

require_once __DIR__ . '/../../config/config.php';

// Si ya está logueado, redirigir al dashboard
if (estaLogueado()) {
    redirect('/views/dashboard.php');
}

$errores = [];
$success = '';

$nombre = '';
$email = '';
$telefono = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../models/Usuario.php';

    $nombre = sanitizar($_POST['nombre'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($nombre)) {
        $errores[] = 'El nombre es obligatorio';
    }

    if (empty($email) || !validarEmail($email)) {
        $errores[] = 'El email es obligatorio y debe ser válido';
    }

    if (empty($password) || strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres';
    }

    if ($password !== $password_confirm) {
        $errores[] = 'Las contraseñas no coinciden';
    }

    if (empty($errores)) {
        $usuario = new Usuario();

        if ($usuario->emailExiste($email)) {
            $errores[] = 'El email ya está registrado';
        } else {
            $id = $usuario->crear([
                'nombre' => $nombre,
                'email' => $email,
                'password' => $password,
                'rol' => ROL_VENDEDOR,
                'telefono' => $telefono,
                'estado' => 'activo'
            ]);

            if ($id) {
                setFlashMessage('success', 'Cuenta creada correctamente. Inicia sesión.');
                redirect('/views/login.php');
            } else {
                $errores[] = 'No se pudo crear la cuenta. Intenta nuevamente.';
            }
        }
    }
}

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            max-width: 520px;
            width: 100%;
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .register-header i {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .register-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .register-body {
            padding: 40px 30px;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            padding: 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: transform 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .input-group-text {
            border: 2px solid #e0e0e0;
            border-right: none;
            background: white;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="bi bi-person-plus"></i>
                <h2>Crear Cuenta</h2>
                <p class="mb-0"><?php echo APP_NAME; ?></p>
            </div>
            <div class="register-body">
                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['tipo']; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($flash['mensaje']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errores as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre completo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="password_confirm" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-register w-100">
                        <i class="bi bi-check-circle"></i> Crear Cuenta
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>/views/login.php" class="text-decoration-none">
                        <i class="bi bi-box-arrow-in-right"></i> Ya tengo cuenta
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-3 text-white">
            <small>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
