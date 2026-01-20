<?php
/**
 * Ver Proveedor
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Proveedor.php';

$page_title = 'Detalle de Proveedor - ' . APP_NAME;

$model = new Proveedor();
$id = intval($_GET['id'] ?? 0);
$proveedor = $model->obtenerPorId($id);

if (!$proveedor) {
    setFlashMessage('error', 'Proveedor no encontrado');
    redirect('/views/proveedores/lista.php');
}

$stats = $model->obtenerEstadisticas($id);
$productos = $model->obtenerProductos($id);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-building"></i> Detalle de Proveedor</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/views/proveedores/editar.php?id=<?php echo $id; ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="<?php echo BASE_URL; ?>/views/proveedores/lista.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Total productos</h6><h3><?php echo $stats['total_productos']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Disponibles</h6><h3><?php echo $stats['disponibles']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Vendidos</h6><h3><?php echo $stats['vendidos']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-warning border-4 shadow-sm"><div class="card-body"><h6>En crédito</h6><h3><?php echo $stats['en_credito']; ?></h3></div></div></div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Nombre:</strong> <?php echo htmlspecialchars($proveedor['nombre']); ?></div>
                <div class="col-md-4"><strong>Empresa:</strong> <?php echo htmlspecialchars($proveedor['empresa']); ?></div>
                <div class="col-md-4"><strong>NIT/Cédula:</strong> <?php echo htmlspecialchars($proveedor['nit_cedula']); ?></div>
                <div class="col-md-4"><strong>Teléfono:</strong> <?php echo htmlspecialchars($proveedor['telefono']); ?></div>
                <div class="col-md-4"><strong>Email:</strong> <?php echo htmlspecialchars($proveedor['email']); ?></div>
                <div class="col-md-4"><strong>Ciudad:</strong> <?php echo htmlspecialchars($proveedor['ciudad']); ?></div>
                <div class="col-md-8"><strong>Dirección:</strong> <?php echo htmlspecialchars($proveedor['direccion']); ?></div>
                <div class="col-md-4"><strong>Estado:</strong> <?php echo ucfirst($proveedor['estado']); ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-phone"></i> Productos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>IMEI</th>
                            <th>Modelo</th>
                            <th>Estado</th>
                            <th>Precio Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Sin productos</td></tr>
                        <?php else: ?>
                            <?php foreach ($productos as $prod): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($prod['imei']); ?></td>
                                    <td><?php echo htmlspecialchars($prod['modelo'] . ' ' . $prod['capacidad']); ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $prod['estado'])); ?></td>
                                    <td><?php echo formatearMoneda($prod['precio_venta']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
