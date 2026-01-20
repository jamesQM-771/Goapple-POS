<?php
/**
 * Lista de Ventas
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Venta.php';

$page_title = 'Ventas - ' . APP_NAME;

$model = new Venta();
$ventas = $model->obtenerTodos($_GET);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h1><i class="bi bi-receipt"></i> Historial de Ventas</h1>
        <a href="<?php echo BASE_URL; ?>/views/ventas/nueva.php" class="btn btn-primary">
            <i class="bi bi-cart-plus"></i> Nueva Venta
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="buscar" placeholder="Número, cliente" value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="tipo_venta">
                        <option value="">Tipo</option>
                        <option value="contado" <?php echo ($_GET['tipo_venta'] ?? '') === 'contado' ? 'selected' : ''; ?>>Contado</option>
                        <option value="credito" <?php echo ($_GET['tipo_venta'] ?? '') === 'credito' ? 'selected' : ''; ?>>Crédito</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="estado">
                        <option value="">Estado</option>
                        <option value="completada" <?php echo ($_GET['estado'] ?? '') === 'completada' ? 'selected' : ''; ?>>Completada</option>
                        <option value="cancelada" <?php echo ($_GET['estado'] ?? '') === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="fecha_desde" value="<?php echo htmlspecialchars($_GET['fecha_desde'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="fecha_hasta" value="<?php echo htmlspecialchars($_GET['fecha_hasta'] ?? ''); ?>">
                </div>
                <div class="col-md-1">
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
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['numero_venta']); ?></td>
                            <td><?php echo formatearFechaHora($v['fecha_venta']); ?></td>
                            <td><?php echo htmlspecialchars($v['cliente_nombre']); ?></td>
                            <td><?php echo ucfirst($v['tipo_venta']); ?></td>
                            <td><?php echo formatearMoneda($v['total']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $v['estado'] === 'completada' ? 'success' : ($v['estado'] === 'cancelada' ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($v['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-info" href="<?php echo BASE_URL; ?>/views/ventas/detalle.php?id=<?php echo $v['id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if ($v['estado'] === 'completada' && esAdmin()): ?>
                                    <button class="btn btn-sm btn-danger" onclick="cancelarVenta(<?php echo $v['id']; ?>)">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function cancelarVenta(id) {
    confirmarEliminacion('¿Cancelar esta venta?').then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo BASE_URL; ?>/controllers/api.php?module=ventas&action=cancelar&id=${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        mostrarExito(data.message || 'Venta cancelada');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        mostrarError(data.message || 'No se pudo cancelar');
                    }
                });
        }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
