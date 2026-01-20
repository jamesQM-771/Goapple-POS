<?php
/**
 * Lista de Usuarios (solo admin)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Usuarios - ' . APP_NAME;

$model = new Usuario();
$usuarios = $model->obtenerTodos($_GET);
$stats = $model->obtenerEstadisticas();

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h1><i class="bi bi-person-badge"></i> Usuarios</h1>
        <a href="<?php echo BASE_URL; ?>/views/usuarios/nuevo.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Usuario
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Total</h6><h3><?php echo $stats['total']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-danger border-4 shadow-sm"><div class="card-body"><h6>Admins</h6><h3><?php echo $stats['administradores']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Vendedores</h6><h3><?php echo $stats['vendedores']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Activos</h6><h3><?php echo $stats['activos']; ?></h3></div></div></div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table id="tabla" class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo ucfirst($u['rol']); ?></td>
                            <td><?php echo htmlspecialchars($u['telefono']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $u['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($u['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-info" href="<?php echo BASE_URL; ?>/views/usuarios/ver.php?id=<?php echo $u['id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-warning" href="<?php echo BASE_URL; ?>/views/usuarios/editar.php?id=<?php echo $u['id']; ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?php echo $u['id']; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function eliminarUsuario(id) {
    confirmarEliminacion('¿Eliminar este usuario?').then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo BASE_URL; ?>/controllers/api.php?module=usuarios&action=delete&id=${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        mostrarExito(data.message || 'Usuario eliminado');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        mostrarError(data.message || 'No se pudo eliminar');
                    }
                });
        }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
