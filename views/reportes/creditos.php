<?php
/**
 * Reporte de Créditos
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

$page_title = 'Reporte de Créditos - ' . APP_NAME;

$model = new Credito();
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

$stats = $model->obtenerEstadisticas($fecha_desde ?: null, $fecha_hasta ?: null);
$creditos = $model->obtenerTodos(['estado' => $_GET['estado'] ?? '']);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="bi bi-file-earmark-bar-graph"></i> Reporte de Créditos</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="date" class="form-control" name="fecha_desde" value="<?php echo htmlspecialchars($fecha_desde); ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="fecha_hasta" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
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

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Total créditos</h6><h3><?php echo $stats['total_creditos'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Activos</h6><h3><?php echo $stats['activos'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-danger border-4 shadow-sm"><div class="card-body"><h6>En mora</h6><h3><?php echo $stats['en_mora'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Saldo por cobrar</h6><h3><?php echo formatearMoneda($stats['saldo_por_cobrar'] ?? 0); ?></h3></div></div></div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($creditos as $cr): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cr['numero_credito']); ?></td>
                            <td><?php echo htmlspecialchars($cr['cliente_nombre']); ?></td>
                            <td><?php echo formatearMoneda($cr['monto_total']); ?></td>
                            <td><?php echo formatearMoneda($cr['saldo_pendiente']); ?></td>
                            <td><?php echo ucfirst($cr['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
