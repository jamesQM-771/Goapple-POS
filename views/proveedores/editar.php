<?php
/**
 * Editar Proveedor
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Proveedor.php';

$page_title = 'Editar Proveedor - ' . APP_NAME;

$model = new Proveedor();
$id = intval($_GET['id'] ?? 0);
$proveedor = $model->obtenerPorId($id);

if (!$proveedor) {
    setFlashMessage('error', 'Proveedor no encontrado');
    redirect('/views/proveedores/lista.php');
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => sanitizar($_POST['nombre'] ?? ''),
        'empresa' => sanitizar($_POST['empresa'] ?? ''),
        'nit_cedula' => sanitizar($_POST['nit_cedula'] ?? ''),
        'telefono' => sanitizar($_POST['telefono'] ?? ''),
        'email' => sanitizar($_POST['email'] ?? ''),
        'direccion' => sanitizar($_POST['direccion'] ?? ''),
        'ciudad' => sanitizar($_POST['ciudad'] ?? ''),
        'pais' => sanitizar($_POST['pais'] ?? 'Colombia'),
        'estado' => sanitizar($_POST['estado'] ?? 'activo')
    ];

    if (empty($datos['nombre'])) $errores[] = 'El nombre es obligatorio';
    if (empty($datos['nit_cedula'])) $errores[] = 'El NIT/Cédula es obligatorio';
    if (!empty($datos['email']) && !validarEmail($datos['email'])) $errores[] = 'El email no es válido';

    if ($model->nitExiste($datos['nit_cedula'], $id)) $errores[] = 'El NIT/Cédula ya está registrado';

    if (empty($errores)) {
        if ($model->actualizar($id, $datos)) {
            setFlashMessage('success', 'Proveedor actualizado correctamente');
            redirect('/views/proveedores/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo actualizar el proveedor';
        }
    }

    $proveedor = array_merge($proveedor, $datos);
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil"></i> Editar Proveedor</h1>
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
                    <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($proveedor['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control" name="empresa" value="<?php echo htmlspecialchars($proveedor['empresa']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIT/Cédula</label>
                    <input type="text" class="form-control" name="nit_cedula" value="<?php echo htmlspecialchars($proveedor['nit_cedula']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($proveedor['telefono']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($proveedor['email']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control" name="ciudad" value="<?php echo htmlspecialchars($proveedor['ciudad']); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="direccion" value="<?php echo htmlspecialchars($proveedor['direccion']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">País</label>
                    <input type="text" class="form-control" name="pais" value="<?php echo htmlspecialchars($proveedor['pais']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="activo" <?php echo $proveedor['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $proveedor['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/proveedores/ver.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
