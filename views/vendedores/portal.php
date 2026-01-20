<?php
/**
 * Portal de Vendedor
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Comision.php';
require_once __DIR__ . '/../../models/Venta.php';

if (!estaLogueado()) {
    redirect('/views/login.php');
}

if (!esVendedor()) {
    redirect('/views/dashboard.php');
}

$page_title = 'Portal de Vendedor - ' . APP_NAME;

$mes = intval($_GET['mes'] ?? date('n'));
$anio = intval($_GET['anio'] ?? date('Y'));

$comisionModel = new Comision();
$ventaModel = new Venta();

$usuario_id = $_SESSION['usuario_id'];

// Calcular comisión del mes si no existe
$comisionModel->calcularMensual($mes, $anio, $usuario_id);

$comisiones = $comisionModel->obtenerPorVendedor($usuario_id);
$ventasMes = $comisionModel->obtenerVentasMes($usuario_id, $mes, $anio);

$comisionActual = null;
foreach ($comisiones as $c) {
    if ((int)$c['mes'] === $mes && (int)$c['anio'] === $anio) {
        $comisionActual = $c;
        break;
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person-badge"></i> Portal de Vendedor</h1>
        <form method="GET" class="d-flex gap-2">
            <select name="mes" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php echo $m === $mes ? 'selected' : ''; ?>><?php echo $m; ?></option>
                <?php endfor; ?>
            </select>
            <select name="anio" class="form-select">
                <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y === $anio ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-outline-primary">Ver</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Total Ventas</h6><h3><?php echo $comisionActual['total_ventas'] ?? 0; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Total Vendido</h6><h3><?php echo formatearMoneda($comisionActual['total_vendido'] ?? 0); ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-warning border-4 shadow-sm"><div class="card-body"><h6>Comisión</h6><h3><?php echo formatearMoneda($comisionActual['total_pagar'] ?? 0); ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Estado</h6><h3><?php echo ucfirst($comisionActual['estado'] ?? 'pendiente'); ?></h3></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-white"><h5 class="mb-0">Ventas del Mes</h5></div>
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
                                <?php if (empty($ventasMes)): ?>
                                    <tr><td colspan="4" class="text-center text-muted">Sin ventas</td></tr>
                                <?php else: ?>
                                    <?php foreach ($ventasMes as $v): ?>
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
                <div class="card-header bg-white"><h5 class="mb-0">Historial de Comisiones</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($comisiones)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">Sin comisiones</td></tr>
                                <?php else: ?>
                                    <?php foreach ($comisiones as $c): ?>
                                        <tr>
                                            <td><?php echo str_pad($c['mes'], 2, '0', STR_PAD_LEFT) . '/' . $c['anio']; ?></td>
                                            <td><?php echo formatearMoneda($c['total_pagar']); ?></td>
                                            <td><?php echo ucfirst($c['estado']); ?></td>
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
