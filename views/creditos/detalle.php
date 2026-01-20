<?php
/**
 * Detalle de Crédito
 * Sistema POS GOapple
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

require_login();

$id = $_GET['id'] ?? 0;
$creditoModel = new Credito();
$credito = $creditoModel->obtenerPorId($id);

if (!$credito) {
    header("Location: " . BASE_URL . "views/creditos/");
    exit();
}

$page_title = "Detalle de Crédito";
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-credit-card me-2"></i>Crédito - Venta #<?= $credito['venta_id'] ?></h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= BASE_URL ?>views/creditos/" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Plan de Cuotas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Monto Cuota</th>
                                    <th>Fecha Pago</th>
                                    <th>Monto Pagado</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($credito['cuotas'] as $cuota): ?>
                                <tr>
                                    <td><?= $cuota['numero_cuota'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($cuota['fecha_vencimiento'])) ?></td>
                                    <td><?= CURRENCY_SYMBOL . number_format($cuota['monto_cuota'], 0, ',', '.') ?></td>
                                    <td>
                                        <?= $cuota['fecha_pago'] ? date('d/m/Y', strtotime($cuota['fecha_pago'])) : '-' ?>
                                    </td>
                                    <td>
                                        <?= $cuota['monto_pagado'] ? CURRENCY_SYMBOL . number_format($cuota['monto_pagado'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($cuota['estado'] == 'pagada'): ?>
                                            <span class="badge bg-success">Pagada</span>
                                        <?php elseif ($cuota['estado'] == 'vencida'): ?>
                                            <span class="badge bg-danger">Vencida</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Historial de Pagos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($credito['pagos'])): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay pagos registrados</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($credito['pagos'] as $pago): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                        <td><strong><?= CURRENCY_SYMBOL . number_format($pago['monto'], 0, ',', '.') ?></strong></td>
                                        <td><?= ucfirst($pago['metodo_pago']) ?></td>
                                        <td><?= htmlspecialchars($pago['observaciones']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Crédito</h5>
                </div>
                <div class="card-body">
                    <p><strong>Cliente:</strong><br>
                    <a href="<?= BASE_URL ?>views/clientes/detalle.php?id=<?= $credito['cliente_id'] ?>">
                        <?= htmlspecialchars($credito['cliente_nombre']) ?>
                    </a></p>
                    
                    <p><strong>Fecha de Venta:</strong><br>
                    <?= date('d/m/Y', strtotime($credito['fecha_venta'])) ?></p>
                    
                    <p><strong>Monto Total:</strong><br>
                    <span class="h4"><?= CURRENCY_SYMBOL . number_format($credito['monto_total'], 0, ',', '.') ?></span></p>
                    
                    <hr>
                    
                    <p><strong>Monto Pagado:</strong><br>
                    <span class="text-success h5"><?= CURRENCY_SYMBOL . number_format($credito['monto_pagado'], 0, ',', '.') ?></span></p>
                    
                    <p><strong>Saldo Pendiente:</strong><br>
                    <span class="text-danger h5"><?= CURRENCY_SYMBOL . number_format($credito['monto_pendiente'], 0, ',', '.') ?></span></p>
                    
                    <hr>
                    
                    <p><strong>Número de Cuotas:</strong> <?= $credito['numero_cuotas'] ?></p>
                    <p><strong>Cuotas Pagadas:</strong> <?= $credito['cuotas_pagadas'] ?></p>
                    <p><strong>Tasa de Interés:</strong> <?= $credito['tasa_interes'] ?>%</p>
                    
                    <p><strong>Estado:</strong><br>
                    <?php if ($credito['estado'] == 'pagado'): ?>
                        <span class="badge bg-success">Pagado</span>
                    <?php elseif ($credito['estado'] == 'activo'): ?>
                        <span class="badge bg-warning">Activo</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Vencido</span>
                    <?php endif; ?>
                    </p>
                    
                    <?php if ($credito['estado'] != 'pagado'): ?>
                    <div class="d-grid mt-4">
                        <button class="btn btn-success" onclick="registrarPago()">
                            <i class="bi bi-cash"></i> Registrar Pago
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar pago -->
<div class="modal fade" id="pagoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= BASE_URL ?>views/creditos/registrar_pago.php">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="credito_id" value="<?= $credito['id'] ?>">
                    
                    <div class="alert alert-info">
                        <strong>Saldo Pendiente:</strong> 
                        <?= CURRENCY_SYMBOL . number_format($credito['monto_pendiente'], 0, ',', '.') ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Monto a Pagar *</label>
                        <input type="number" name="monto" class="form-control" 
                               min="1" max="<?= $credito['monto_pendiente'] ?>" 
                               step="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Método de Pago *</label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function registrarPago() {
    new bootstrap.Modal(document.getElementById('pagoModal')).show();
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
