<?php
/**
 * Reporte de Comisiones (Admin)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Comision.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Reporte de Comisiones - ' . APP_NAME;

$mes = intval($_GET['mes'] ?? date('n'));
$anio = intval($_GET['anio'] ?? date('Y'));

$comisionModel = new Comision();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generar'])) {
        $comisionModel->calcularMensual($mes, $anio);
        setFlashMessage('success', 'Comisiones calculadas correctamente');
        redirect('/views/comisiones/reportes.php?mes=' . $mes . '&anio=' . $anio);
    }

    if (!empty($_POST['pagar_id'])) {
        $id = intval($_POST['pagar_id']);
        $ok = $comisionModel->marcarPagada($id, $_SESSION['usuario_id']);
        setFlashMessage($ok ? 'success' : 'error', $ok ? 'Comisión marcada como pagada' : 'No se pudo marcar como pagada');
        redirect('/views/comisiones/reportes.php?mes=' . $mes . '&anio=' . $anio);
    }
}

$resumen = $comisionModel->obtenerResumen($mes, $anio);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-cash-coin"></i> Reporte de Comisiones</h1>
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

    <div class="d-flex justify-content-end mb-3">
        <form method="POST">
            <button type="submit" name="generar" class="btn btn-primary">
                <i class="bi bi-calculator"></i> Calcular comisiones del mes
            </button>
        </form>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white"><h5 class="mb-0">Resumen</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Vendedor</th>
                            <th>Ventas</th>
                            <th>Total vendido</th>
                            <th>%</th>
                            <th>Comisión</th>
                            <th>Bono</th>
                            <th>Descuento</th>
                            <th>Retención</th>
                            <th>Total pagar</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($resumen)): ?>
                            <tr><td colspan="11" class="text-center text-muted">Sin datos</td></tr>
                        <?php else: ?>
                            <?php foreach ($resumen as $r): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($r['vendedor_nombre']); ?></td>
                                    <td><?php echo intval($r['total_ventas']); ?></td>
                                    <td><?php echo formatearMoneda($r['total_vendido']); ?></td>
                                    <td><?php echo number_format($r['porcentaje'], 2); ?>%</td>
                                    <td><?php echo formatearMoneda($r['comision_base']); ?></td>
                                    <td><?php echo formatearMoneda($r['bono']); ?></td>
                                    <td><?php echo formatearMoneda($r['descuento']); ?></td>
                                    <td><?php echo formatearMoneda($r['retencion']); ?></td>
                                    <td><strong><?php echo formatearMoneda($r['total_pagar']); ?></strong></td>
                                    <td><?php echo ucfirst($r['estado']); ?></td>
                                    <td>
                                        <?php if ($r['estado'] === 'pendiente'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="pagar_id" value="<?php echo $r['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Marcar pagada</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Pagada</span>
                                        <?php endif; ?>
                                    </td>
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
