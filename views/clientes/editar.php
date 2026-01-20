<?php
/**
 * Editar Cliente
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Cliente.php';

$page_title = 'Editar Cliente - ' . APP_NAME;

$clienteModel = new Cliente();
$id = intval($_GET['id'] ?? 0);
$cliente = $clienteModel->obtenerPorId($id);

if (!$cliente) {
    setFlashMessage('error', 'Cliente no encontrado');
    redirect('/views/clientes/lista.php');
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => sanitizar($_POST['nombre'] ?? ''),
        'cedula' => sanitizar($_POST['cedula'] ?? ''),
        'telefono' => sanitizar($_POST['telefono'] ?? ''),
        'email' => sanitizar($_POST['email'] ?? ''),
        'direccion' => sanitizar($_POST['direccion'] ?? ''),
        'ciudad' => sanitizar($_POST['ciudad'] ?? ''),
        'estado' => sanitizar($_POST['estado'] ?? CLIENTE_ACTIVO),
        'limite_credito' => floatval($_POST['limite_credito'] ?? 0),
        'credito_disponible' => floatval($_POST['credito_disponible'] ?? 0),
        'notas' => sanitizar($_POST['notas'] ?? '')
    ];

    if (empty($datos['nombre'])) {
        $errores[] = 'El nombre es obligatorio';
    }
    if (empty($datos['cedula'])) {
        $errores[] = 'La cédula es obligatoria';
    }
    if (empty($datos['telefono'])) {
        $errores[] = 'El teléfono es obligatorio';
    }
    if (!empty($datos['email']) && !validarEmail($datos['email'])) {
        $errores[] = 'El email no es válido';
    }

    if ($clienteModel->cedulaExiste($datos['cedula'], $id)) {
        $errores[] = 'La cédula ya está registrada';
    }

    if (empty($errores)) {
        if ($clienteModel->actualizar($id, $datos)) {
            setFlashMessage('success', 'Cliente actualizado correctamente');
            redirect('/views/clientes/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo actualizar el cliente';
        }
    }

    $cliente = array_merge($cliente, $datos);
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil"></i> Editar Cliente</h1>
        <a href="<?php echo BASE_URL; ?>/views/clientes/lista.php" class="btn btn-outline-secondary">
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
                    <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cédula</label>
                    <input type="text" class="form-control" name="cedula" value="<?php echo htmlspecialchars($cliente['cedula']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control" name="ciudad" value="<?php echo htmlspecialchars($cliente['ciudad'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="activo" <?php echo ($cliente['estado'] ?? '') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="moroso" <?php echo ($cliente['estado'] ?? '') === 'moroso' ? 'selected' : ''; ?>>Moroso</option>
                        <option value="bloqueado" <?php echo ($cliente['estado'] ?? '') === 'bloqueado' ? 'selected' : ''; ?>>Bloqueado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Límite Crédito</label>
                    <input type="number" class="form-control" name="limite_credito" value="<?php echo htmlspecialchars($cliente['limite_credito'] ?? ''); ?>" min="0" step="0.01">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Crédito Disponible</label>
                    <input type="number" class="form-control" name="credito_disponible" value="<?php echo htmlspecialchars($cliente['credito_disponible'] ?? ''); ?>" min="0" step="0.01">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea class="form-control" name="notas" rows="3"><?php echo htmlspecialchars($cliente['notas'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/clientes/ver.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
