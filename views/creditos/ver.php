<?php
/**
 * Ver Crédito
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

$page_title = 'Detalle de Crédito - ' . APP_NAME;

$model = new Credito();
$id = intval($_GET['id'] ?? 0);
$credito = $model->obtenerPorId($id);

if (!$credito) {
    setFlashMessage('error', 'Crédito no encontrado');
    redirect('/views/creditos/lista.php');
}

$pagos = $model->obtenerPagos($id);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-credit-card"></i> Detalle de Crédito</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/views/creditos/pagos.php?credito_id=<?php echo $id; ?>" class="btn btn-success">
                <i class="bi bi-cash-coin"></i> Registrar Pago
            </a>
            <a href="<?php echo BASE_URL; ?>/views/creditos/lista.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Número:</strong> <?php echo htmlspecialchars($credito['numero_credito']); ?></div>
                <div class="col-md-4"><strong>Cliente:</strong> <?php echo htmlspecialchars($credito['cliente_nombre']); ?></div>
                <div class="col-md-4"><strong>Venta:</strong> <?php echo htmlspecialchars($credito['numero_venta']); ?></div>
                <div class="col-md-4"><strong>Monto total:</strong> <?php echo formatearMoneda($credito['monto_total']); ?></div>
                <div class="col-md-4"><strong>Saldo:</strong> <?php echo formatearMoneda($credito['saldo_pendiente']); ?></div>
                <div class="col-md-4"><strong>Estado:</strong> <?php echo ucfirst($credito['estado']); ?></div>
                <div class="col-md-4"><strong>Cuota:</strong> <?php echo formatearMoneda($credito['valor_cuota']); ?></div>
                <div class="col-md-4"><strong>Intereses:</strong> <?php echo formatearMoneda($credito['total_intereses']); ?></div>
                <div class="col-md-4"><strong>Total pagado:</strong> <?php echo formatearMoneda($credito['total_pagado']); ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Pagos</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Recibo</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Forma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagos)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Sin pagos</td></tr>
                    <?php else: ?>
                        <?php foreach ($pagos as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['numero_recibo']); ?></td>
                                <td><?php echo formatearFechaHora($p['fecha_pago']); ?></td>
                                <td><?php echo formatearMoneda($p['monto_pago']); ?></td>
                                <td><?php echo htmlspecialchars($p['forma_pago']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
