<?php
/**
 * Dashboard de Vendedor
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Comision.php';

if (!estaLogueado()) {
    redirect('/views/login.php');
}

if (!esVendedor()) {
    redirect('/views/dashboard.php');
}

$page_title = 'Dashboard Vendedor - ' . APP_NAME;

$ventaModel = new Venta();
$comisionModel = new Comision();

$usuario_id = $_SESSION['usuario_id'];
$fecha_inicio = date('Y-m-01');
$fecha_fin = date('Y-m-t');

$ventasMes = $ventaModel->obtenerPorVendedor($usuario_id, $fecha_inicio, $fecha_fin);
$total_ventas = count($ventasMes);
$total_vendido = 0;
foreach ($ventasMes as $v) {
    $total_vendido += floatval($v['total']);
}
$ticket_promedio = $total_ventas > 0 ? ($total_vendido / $total_ventas) : 0;

$mes = intval(date('n'));
$anio = intval(date('Y'));
$comisionModel->calcularMensual($mes, $anio, $usuario_id);
$comisiones = $comisionModel->obtenerPorVendedor($usuario_id);
$comisionActual = null;
foreach ($comisiones as $c) {
    if ((int)$c['mes'] === $mes && (int)$c['anio'] === $anio) {
        $comisionActual = $c;
        break;
    }
}

$ventasRecientes = array_slice($ventasMes, 0, 5);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-speedometer2"></i> Dashboard Vendedor</h1>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>/views/ventas/nueva.php" class="btn btn-primary">
                <i class="bi bi-cart-plus"></i> Nueva Venta
            </a>
            <a href="<?php echo BASE_URL; ?>/views/vendedores/portal.php" class="btn btn-outline-secondary">
                <i class="bi bi-person-badge"></i> Mi Portal
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Ventas del Mes</h6><h3><?php echo $total_ventas; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Total Vendido</h6><h3><?php echo formatearMoneda($total_vendido); ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-warning border-4 shadow-sm"><div class="card-body"><h6>Ticket Promedio</h6><h3><?php echo formatearMoneda($ticket_promedio); ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Comisión Estimada</h6><h3><?php echo formatearMoneda($comisionActual['total_pagar'] ?? 0); ?></h3></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-white"><h5 class="mb-0">Ventas Recientes</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
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
                                <?php if (empty($ventasRecientes)): ?>
                                    <tr><td colspan="4" class="text-center text-muted">Sin ventas</td></tr>
                                <?php else: ?>
                                    <?php foreach ($ventasRecientes as $v): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($v['numero_venta']); ?></td>
                                            <td><?php echo formatearFechaHora($v['fecha_venta']); ?></td>
                                            <td><?php echo htmlspecialchars($v['cliente_nombre']); ?></td>
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
        <div class="col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-white"><h5 class="mb-0">Accesos Rápidos</h5></div>
                <div class="card-body d-grid gap-2">
                    <a class="btn btn-outline-primary" href="<?php echo BASE_URL; ?>/views/ventas/lista.php"><i class="bi bi-receipt me-2"></i> Mis Ventas</a>
                    <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/views/devoluciones/lista.php"><i class="bi bi-arrow-counterclockwise me-2"></i> Devoluciones</a>
                    <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/views/creditos/lista.php"><i class="bi bi-credit-card me-2"></i> Créditos</a>
                    <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/views/clientes/lista.php"><i class="bi bi-people me-2"></i> Clientes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
