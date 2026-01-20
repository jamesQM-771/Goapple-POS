<?php
/**
 * Editar Usuario (solo admin)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Editar Usuario - ' . APP_NAME;

$model = new Usuario();
$id = intval($_GET['id'] ?? 0);
$usuario = $model->obtenerPorId($id);

if (!$usuario) {
    setFlashMessage('error', 'Usuario no encontrado');
    redirect('/views/usuarios/lista.php');
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => sanitizar($_POST['nombre'] ?? ''),
        'email' => sanitizar($_POST['email'] ?? ''),
        'telefono' => sanitizar($_POST['telefono'] ?? ''),
        'rol' => sanitizar($_POST['rol'] ?? ROL_VENDEDOR),
        'estado' => sanitizar($_POST['estado'] ?? 'activo')
    ];

    if (empty($datos['nombre'])) $errores[] = 'El nombre es obligatorio';
    if (empty($datos['email']) || !validarEmail($datos['email'])) $errores[] = 'Email inválido';

    if ($model->emailExiste($datos['email'], $id)) $errores[] = 'El email ya está registrado';

    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errores[] = 'Contraseña mínima de 6 caracteres';
        } else {
            $datos['password'] = $_POST['password'];
        }
    }

    if (empty($errores)) {
        if ($model->actualizar($id, $datos)) {
            setFlashMessage('success', 'Usuario actualizado correctamente');
            redirect('/views/usuarios/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo actualizar el usuario';
        }
    }

    $usuario = array_merge($usuario, $datos);
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil"></i> Editar Usuario</h1>
        <a href="<?php echo BASE_URL; ?>/views/usuarios/lista.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select class="form-select" name="rol">
                        <option value="administrador" <?php echo $usuario['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="vendedor" <?php echo $usuario['rol'] === 'vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="activo" <?php echo $usuario['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $usuario['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nueva contraseña (opcional)</label>
                    <input type="password" class="form-control" name="password">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/usuarios/ver.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
