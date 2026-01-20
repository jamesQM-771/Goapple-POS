<?php
/**
 * Perfil de Usuario
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';

$page_title = 'Mi Perfil - ' . APP_NAME;

$usuario = usuarioActual();
$model = new Usuario();
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = $_POST['password_actual'] ?? '';
    $nueva = $_POST['password_nueva'] ?? '';
    $confirmar = $_POST['password_confirm'] ?? '';

    if (empty($actual) || empty($nueva) || empty($confirmar)) {
        $errores[] = 'Debe completar todos los campos';
    } elseif ($nueva !== $confirmar) {
        $errores[] = 'Las contraseñas no coinciden';
    } elseif (strlen($nueva) < 6) {
        $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres';
    }

    if (empty($errores)) {
        $ok = $model->cambiarPassword($usuario['id'], $actual, $nueva);
        if ($ok) {
            setFlashMessage('success', 'Contraseña actualizada correctamente');
            redirect('/views/perfil.php');
        } else {
            $errores[] = 'La contraseña actual es incorrecta';
        }
    }
}

include __DIR__ . '/layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="bi bi-person"></i> Mi Perfil</h1>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Información</h6>
                </div>
                <div class="card-body">
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                    <p><strong>Rol:</strong> <?php echo ucfirst($usuario['rol']); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Cambiar contraseña</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Contraseña actual</label>
                            <input type="password" class="form-control" name="password_actual" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" name="password_nueva" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" name="password_confirm" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Actualizar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
