<?php
/**
 * Nuevo Proveedor
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Proveedor.php';

$page_title = 'Nuevo Proveedor - ' . APP_NAME;

$model = new Proveedor();
$errores = [];

$valores = [
    'nombre' => '',
    'empresa' => '',
    'nit_cedula' => '',
    'telefono' => '',
    'email' => '',
    'direccion' => '',
    'ciudad' => '',
    'pais' => 'Colombia',
    'estado' => 'activo'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($valores as $k => $v) {
        $valores[$k] = sanitizar($_POST[$k] ?? $v);
    }

    if (empty($valores['nombre'])) $errores[] = 'El nombre es obligatorio';
    if (empty($valores['nit_cedula'])) $errores[] = 'El NIT/Cédula es obligatorio';
    if (!empty($valores['email']) && !validarEmail($valores['email'])) $errores[] = 'El email no es válido';

    if ($model->nitExiste($valores['nit_cedula'])) $errores[] = 'El NIT/Cédula ya está registrado';

    if (empty($errores)) {
        $id = $model->crear($valores);
        if ($id) {
            setFlashMessage('success', 'Proveedor creado correctamente');
            redirect('/views/proveedores/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo crear el proveedor';
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-plus-circle"></i> Nuevo Proveedor</h1>
        <a href="<?php echo BASE_URL; ?>/views/proveedores/lista.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($valores['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control" name="empresa" value="<?php echo htmlspecialchars($valores['empresa']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIT/Cédula</label>
                    <input type="text" class="form-control" name="nit_cedula" value="<?php echo htmlspecialchars($valores['nit_cedula']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($valores['telefono']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($valores['email']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control" name="ciudad" value="<?php echo htmlspecialchars($valores['ciudad']); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="direccion" value="<?php echo htmlspecialchars($valores['direccion']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">País</label>
                    <input type="text" class="form-control" name="pais" value="<?php echo htmlspecialchars($valores['pais']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="activo" <?php echo $valores['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $valores['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/proveedores/lista.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
