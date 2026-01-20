<?php
/**
 * Créditos en Mora
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

$page_title = 'Créditos en Mora - ' . APP_NAME;

$model = new Credito();
$model->verificarMoras();
$creditos = $model->obtenerEnMora();

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="bi bi-exclamation-triangle"></i> Créditos en Mora</h1>

    <div class="card shadow">
        <div class="card-body">
            <table id="tabla" class="table table-hover">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Saldo</th>
                        <th>Días Mora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($creditos as $cr): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cr['numero_credito']); ?></td>
                            <td><?php echo htmlspecialchars($cr['cliente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($cr['cliente_telefono']); ?></td>
                            <td><?php echo formatearMoneda($cr['saldo_pendiente']); ?></td>
                            <td><span class="badge bg-danger"><?php echo intval($cr['dias_mora']); ?></span></td>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
