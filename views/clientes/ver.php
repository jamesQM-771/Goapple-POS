<?php
/**
 * Ver Cliente
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Cliente.php';

$page_title = 'Detalle del Cliente - ' . APP_NAME;

$clienteModel = new Cliente();
$id = intval($_GET['id'] ?? 0);

$cliente = $clienteModel->obtenerPorId($id);
if (!$cliente) {
    setFlashMessage('error', 'Cliente no encontrado');
    redirect('/views/clientes/lista.php');
}

$stats = $clienteModel->obtenerEstadisticas($id);
$creditos = $clienteModel->obtenerCreditos($id);
$compras = $clienteModel->obtenerHistorialCompras($id);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person"></i> Detalle del Cliente</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/views/clientes/editar.php?id=<?php echo $id; ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="<?php echo BASE_URL; ?>/views/clientes/lista.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <h6>Total Ventas</h6>
                    <h3><?php echo $stats['total_ventas'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <h6>Total Gastado</h6>
                    <h3><?php echo formatearMoneda($stats['total_gastado'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <h6>Créditos</h6>
                    <h3><?php echo $stats['total_creditos'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm">
                <div class="card-body">
                    <h6>Deuda Actual</h6>
                    <h3><?php echo formatearMoneda($stats['deuda_actual'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-person-badge"></i> Información</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?></div>
                <div class="col-md-4"><strong>Cédula:</strong> <?php echo htmlspecialchars($cliente['cedula']); ?></div>
                <div class="col-md-4"><strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['telefono']); ?></div>
                <div class="col-md-4"><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></div>
                <div class="col-md-4"><strong>Ciudad:</strong> <?php echo htmlspecialchars($cliente['ciudad']); ?></div>
                <div class="col-md-4"><strong>Estado:</strong> <?php echo ucfirst($cliente['estado']); ?></div>
                <div class="col-md-6"><strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['direccion']); ?></div>
                <div class="col-md-3"><strong>Límite crédito:</strong> <?php echo formatearMoneda($cliente['limite_credito']); ?></div>
                <div class="col-md-3"><strong>Crédito disponible:</strong> <?php echo formatearMoneda($cliente['credito_disponible']); ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-credit-card"></i> Créditos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Estado</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($creditos)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">Sin créditos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($creditos as $cr): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cr['numero_credito']); ?></td>
                                            <td><?php echo ucfirst($cr['estado']); ?></td>
                                            <td><?php echo formatearMoneda($cr['saldo_pendiente']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-receipt"></i> Historial de compras</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($compras)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">Sin compras</td></tr>
                                <?php else: ?>
                                    <?php foreach ($compras as $v): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($v['numero_venta']); ?></td>
                                            <td><?php echo formatearFechaHora($v['fecha_venta']); ?></td>
                                            <td><?php echo formatearMoneda($v['total']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
