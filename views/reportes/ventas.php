<?php
/**
 * Reporte de Ventas
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Venta.php';

$page_title = 'Reporte de Ventas - ' . APP_NAME;

$model = new Venta();
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

$stats = $model->obtenerEstadisticas($fecha_desde ?: null, $fecha_hasta ?: null);
$ventas = $model->obtenerTodos(['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
$ranking = $model->obtenerRankingVendedores($fecha_desde ?: null, $fecha_hasta ?: null);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="bi bi-graph-up"></i> Reporte de Ventas</h1>

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
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Total ventas</h6><h3><?php echo $stats['total_ventas'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Total vendido</h6><h3><?php echo formatearMoneda($stats['total_vendido'] ?? 0); ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Contado</h6><h3><?php echo $stats['ventas_contado'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-warning border-4 shadow-sm"><div class="card-body"><h6>Crédito</h6><h3><?php echo $stats['ventas_credito'] ?? 0; ?></h3></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-white"><h5 class="mb-0">Ventas</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventas as $v): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($v['numero_venta']); ?></td>
                                    <td><?php echo formatearFechaHora($v['fecha_venta']); ?></td>
                                    <td><?php echo htmlspecialchars($v['cliente_nombre']); ?></td>
                                    <td><?php echo formatearMoneda($v['total']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-white"><h5 class="mb-0">Ranking vendedores</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Vendedor</th>
                                <th>Ventas</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ranking as $r): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                                    <td><?php echo intval($r['total_ventas']); ?></td>
                                    <td><?php echo formatearMoneda($r['total_vendido']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
