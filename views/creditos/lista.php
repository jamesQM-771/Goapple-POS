<?php
/**
 * Lista de Créditos
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

$page_title = 'Créditos - ' . APP_NAME;

$model = new Credito();
$creditos = $model->obtenerTodos($_GET);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h1><i class="bi bi-credit-card"></i> Créditos</h1>
        <a href="<?php echo BASE_URL; ?>/views/creditos/pagos.php" class="btn btn-primary">
            <i class="bi bi-cash-coin"></i> Registrar Pago
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="buscar" placeholder="Número, cliente" value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="estado">
                        <option value="">Estado</option>
                        <option value="activo" <?php echo ($_GET['estado'] ?? '') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="mora" <?php echo ($_GET['estado'] ?? '') === 'mora' ? 'selected' : ''; ?>>Mora</option>
                        <option value="pagado" <?php echo ($_GET['estado'] ?? '') === 'pagado' ? 'selected' : ''; ?>>Pagado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table id="tabla" class="table table-hover">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($creditos)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">No hay créditos registrados</p>
                                    <small>Los créditos se crean al realizar ventas a crédito</small>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($creditos as $cr): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cr['numero_credito']); ?></td>
                            <td><?php echo htmlspecialchars($cr['cliente_nombre']); ?></td>
                            <td><?php echo formatearMoneda($cr['monto_total']); ?></td>
                            <td><?php echo formatearMoneda($cr['saldo_pendiente']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $cr['estado'] === 'activo' ? 'success' : ($cr['estado'] === 'mora' ? 'danger' : 'secondary'); ?>">
                                    <?php echo ucfirst($cr['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-info" href="<?php echo BASE_URL; ?>/views/creditos/ver.php?id=<?php echo $cr['id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-success" href="<?php echo BASE_URL; ?>/views/creditos/pagos.php?credito_id=<?php echo $cr['id']; ?>">
                                    <i class="bi bi-cash-coin"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
