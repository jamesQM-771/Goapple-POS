<?php
/**
 * Página de Login
 */

require_once __DIR__ . '/../config/config.php';

// Si ya está logueado, redirigir al dashboard
if (estaLogueado()) {
    redirect('/dashboard.php');
}

$error = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../models/Usuario.php';
    
    $email = sanitizar($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Por favor ingrese email y contraseña';
    } else {
        $usuario = new Usuario();
        $resultado = $usuario->autenticar($email, $password);

        if ($resultado) {
            // Guardar datos en sesión
            $_SESSION['usuario_id'] = $resultado['id'];
            $_SESSION['usuario_nombre'] = $resultado['nombre'];
            $_SESSION['usuario_email'] = $resultado['email'];
            $_SESSION['usuario_rol'] = $resultado['rol'];
            $_SESSION['ultima_actividad'] = time();

            setFlashMessage('success', '¡Bienvenido ' . $resultado['nombre'] . '!');
            redirect('/views/dashboard.php');
        } else {
            $error = 'Email o contraseña incorrectos';
        }
    }
}

// Obtener mensaje flash si existe
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg-1: #f6f7fb;
            --card-bg: rgba(255,255,255,0.7);
            --muted: #6b7280;
            --accent: #0a84ff; /* azul neutro tipo iOS */
        }

        html,body{height:100%;}
        body{
            margin:0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: radial-gradient(1200px 600px at 10% 10%, rgba(10,132,255,0.06), transparent),
                        radial-gradient(900px 400px at 90% 90%, rgba(52,199,89,0.03), transparent),
                        linear-gradient(180deg, var(--bg-1) 0%, #ffffff 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
            padding:2rem;
        }

        .login-wrap{
            width:100%;
            max-width:420px;
        }

        .glass-card{
            background: linear-gradient(180deg, rgba(255,255,255,0.85), rgba(255,255,255,0.65));
            border-radius:20px;
            padding:2rem;
            box-shadow: 0 12px 40px rgba(12,15,20,0.08);
            border: 1px solid rgba(15,23,42,0.04);
            backdrop-filter: blur(8px) saturate(120%);
        }

        .brand{
            display:flex;
            align-items:center;
            gap:0.9rem;
            justify-content:center;
            margin-bottom:1.25rem;
        }

        .brand .logo{
            width:64px;
            height:64px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:14px;
            background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(245,245,245,0.9));
            box-shadow: 0 6px 18px rgba(12,15,20,0.06);
            font-size:1.9rem;
            color:#111827;
        }

        h1.title{
            margin:0;
            font-size:1.15rem;
            font-weight:700;
            color:#0f172a;
            letter-spacing:-0.2px;
        }

        p.subtitle{
            margin:0;
            color:var(--muted);
            font-size:0.9rem;
        }

        .form-label{
            font-size:0.85rem;
            font-weight:600;
            color:#0f172a;
            margin-bottom:0.45rem;
        }

        .apple-input{
            width:100%;
            padding:0.78rem 1rem;
            border-radius:12px;
            border:1px solid rgba(15,23,42,0.06);
            background:linear-gradient(180deg, #ffffff, #fbfcfd);
            box-shadow: inset 0 -1px 0 rgba(15,23,42,0.02);
            transition: box-shadow 160ms ease, border-color 160ms ease, transform 120ms ease;
            font-size:0.95rem;
        }

        .apple-input:focus{
            outline:none;
            border-color: rgba(10,132,255,0.9);
            box-shadow: 0 6px 18px rgba(10,132,255,0.06);
            transform: translateY(-1px);
        }

        .apple-btn{
            width:100%;
            padding:0.85rem;
            border-radius:12px;
            border:none;
            background: linear-gradient(180deg, var(--accent), #0066d6);
            color:white;
            font-weight:700;
            box-shadow: 0 8px 24px rgba(10,132,255,0.14);
            transition: transform 120ms ease, box-shadow 120ms ease;
            font-size:0.95rem;
        }

        .apple-btn:active{transform:translateY(1px);}

        .secondary-link{
            color:var(--muted);
            font-size:0.9rem;
            text-align:center;
            margin-top:0.85rem;
        }

        .help-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-top:0.65rem;
            gap:0.5rem;
        }

        .signin-apple{
            display:flex;
            align-items:center;
            gap:0.5rem;
            justify-content:center;
            width:100%;
            padding:0.7rem;
            border-radius:12px;
            border:1px solid rgba(15,23,42,0.06);
            background: white;
            color:#111827;
            font-weight:600;
            transition: box-shadow 120ms ease;
        }

        .alert{border-radius:10px;}

        @media (max-width:420px){
            .brand .logo{width:56px;height:56px;font-size:1.6rem}
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="glass-card">
            <div class="brand">
                <div class="logo">
                    <i class="bi bi-apple"></i>
                </div>
                <div>
                    <h1 class="title"><?php echo APP_NAME; ?></h1>
                    <p class="subtitle">Panel de administración</p>
                </div>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['tipo']; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="apple-input" placeholder="usuario@ejemplo.com" required autofocus>
                </div>

                <div class="mb-2">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="apple-input" placeholder="••••••••" required>
                </div>

                <div class="help-row">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label small text-muted" for="remember">Recordarme</label>
                    </div>
                    <div>
                        <a href="#" class="text-decoration-none small text-muted">¿Olvidó su contraseña?</a>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="apple-btn">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                    </button>
                </div>
            </form>

            <div class="mt-3">
                <div class="signin-apple">
                    <i class="bi bi-apple fs-5"></i>
                    Iniciar sesión con Apple
                </div>
            </div>

            <div class="secondary-link">
                <a href="<?php echo BASE_URL; ?>/views/usuarios/registrar.php" class="text-decoration-none">Crear cuenta</a>
            </div>

            <div class="text-center mt-3 small text-muted">
                &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
