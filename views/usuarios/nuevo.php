<?php
/**
 * Nuevo Usuario (solo admin)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Nuevo Usuario - ' . APP_NAME;

$model = new Usuario();
$errores = [];
$valores = [
    'nombre' => '',
    'email' => '',
    'telefono' => '',
    'rol' => ROL_VENDEDOR,
    'estado' => 'activo'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valores['nombre'] = sanitizar($_POST['nombre'] ?? '');
    $valores['email'] = sanitizar($_POST['email'] ?? '');
    $valores['telefono'] = sanitizar($_POST['telefono'] ?? '');
    $valores['rol'] = sanitizar($_POST['rol'] ?? ROL_VENDEDOR);
    $valores['estado'] = sanitizar($_POST['estado'] ?? 'activo');
    $password = $_POST['password'] ?? '';

    if (empty($valores['nombre'])) $errores[] = 'El nombre es obligatorio';
    if (empty($valores['email']) || !validarEmail($valores['email'])) $errores[] = 'Email inválido';
    if (empty($password) || strlen($password) < 6) $errores[] = 'Contraseña mínima de 6 caracteres';

    if ($model->emailExiste($valores['email'])) $errores[] = 'El email ya está registrado';

    if (empty($errores)) {
        $id = $model->crear([
            'nombre' => $valores['nombre'],
            'email' => $valores['email'],
            'password' => $password,
            'rol' => $valores['rol'],
            'telefono' => $valores['telefono'],
            'estado' => $valores['estado']
        ]);

        if ($id) {
            setFlashMessage('success', 'Usuario creado correctamente');
            redirect('/views/usuarios/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo crear el usuario';
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person-plus"></i> Nuevo Usuario</h1>
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
                    <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($valores['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($valores['email']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($valores['telefono']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select class="form-select" name="rol">
                        <option value="administrador" <?php echo $valores['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="vendedor" <?php echo $valores['rol'] === 'vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="activo" <?php echo $valores['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $valores['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/usuarios/lista.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
