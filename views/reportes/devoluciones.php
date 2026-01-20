<?php
/**
 * Reporte de Devoluciones y Cambios
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Devolucion.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Reporte de Devoluciones - ' . APP_NAME;

$model = new Devolucion();
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

$stats = $model->obtenerEstadisticas($fecha_desde ?: null, $fecha_hasta ?: null);
$devoluciones = $model->obtenerTodas([
    'fecha_desde' => $fecha_desde,
    'fecha_hasta' => $fecha_hasta
]);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="bi bi-arrow-counterclockwise"></i> Reporte de Devoluciones y Cambios</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="date" class="form-control" name="fecha_desde" value="<?php echo htmlspecialchars($fecha_desde); ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="fecha_hasta" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Total solicitudes</h6><h3><?php echo $stats['total_devoluciones'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Total reembolsado</h6><h3><?php echo formatearMoneda($stats['total_reembolsado'] ?? 0); ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-warning border-4 shadow-sm"><div class="card-body"><h6>Cambios</h6><h3><?php echo $stats['total_cambios'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Completadas</h6><h3><?php echo $stats['completadas'] ?? 0; ?></h3></div></div></div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white"><h5 class="mb-0">Listado</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Venta</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($devoluciones)): ?>
                            <tr><td colspan="7" class="text-center text-muted">Sin devoluciones</td></tr>
                        <?php else: ?>
                            <?php foreach ($devoluciones as $dev): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dev['numero_devolucion']); ?></td>
                                    <td><?php echo htmlspecialchars($dev['cliente_nombre'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($dev['numero_venta'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($dev['tipo_solicitud'] ?? 'devolucion') === 'cambio' ? 'warning' : 'primary'; ?>">
                                            <?php echo strtoupper($dev['tipo_solicitud'] ?? 'DEVOLUCION'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo ucfirst($dev['estado']); ?></td>
                                    <td><?php echo formatearMoneda($dev['monto_reembolso']); ?></td>
                                    <td><?php echo formatearFechaHora($dev['fecha_solicitud']); ?></td>
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
