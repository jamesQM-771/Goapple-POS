<?php
/**
 * Ver Usuario (solo admin)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Detalle de Usuario - ' . APP_NAME;

$model = new Usuario();
$id = intval($_GET['id'] ?? 0);
$usuario = $model->obtenerPorId($id);

if (!$usuario) {
    setFlashMessage('error', 'Usuario no encontrado');
    redirect('/views/usuarios/lista.php');
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person"></i> Detalle de Usuario</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/views/usuarios/editar.php?id=<?php echo $id; ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="<?php echo BASE_URL; ?>/views/usuarios/lista.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></div>
                <div class="col-md-4"><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></div>
                <div class="col-md-4"><strong>Rol:</strong> <?php echo ucfirst($usuario['rol']); ?></div>
                <div class="col-md-4"><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?></div>
                <div class="col-md-4"><strong>Estado:</strong> <?php echo ucfirst($usuario['estado']); ?></div>
                <div class="col-md-4"><strong>Creado:</strong> <?php echo formatearFechaHora($usuario['fecha_creacion']); ?></div>
                <div class="col-md-4"><strong>Último acceso:</strong> <?php echo formatearFechaHora($usuario['ultimo_acceso']); ?></div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
