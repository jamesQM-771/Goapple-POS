<?php
/**
 * Lista de Proveedores
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Proveedor.php';

$page_title = 'Proveedores - ' . APP_NAME;

$model = new Proveedor();
$proveedores = $model->obtenerTodos($_GET);
$stats = $model->obtenerEstadisticasGenerales();

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h1><i class="bi bi-building"></i> Proveedores</h1>
        <a href="<?php echo BASE_URL; ?>/views/proveedores/nuevo.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Proveedor
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <h6>Total</h6>
                    <h3><?php echo $stats['total']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <h6>Activos</h6>
                    <h3><?php echo $stats['activos']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-secondary border-4 shadow-sm">
                <div class="card-body">
                    <h6>Inactivos</h6>
                    <h3><?php echo $stats['inactivos']; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table id="tabla" class="table table-hover">
                <thead>
                    <tr>
                        <th>NIT/Cédula</th>
                        <th>Nombre</th>
                        <th>Empresa</th>
                        <th>Teléfono</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['nit_cedula']); ?></td>
                            <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($p['empresa']); ?></td>
                            <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($p['ciudad']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $p['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($p['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-info" href="<?php echo BASE_URL; ?>/views/proveedores/ver.php?id=<?php echo $p['id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-warning" href="<?php echo BASE_URL; ?>/views/proveedores/editar.php?id=<?php echo $p['id']; ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="eliminarProveedor(<?php echo $p['id']; ?>)">
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
function eliminarProveedor(id) {
    confirmarEliminacion('¿Eliminar este proveedor?').then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo BASE_URL; ?>/controllers/api.php?module=proveedores&action=delete&id=${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        mostrarExito(data.message || 'Proveedor eliminado');
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
