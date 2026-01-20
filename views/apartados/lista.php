<?php
/**
 * Lista de Apartados
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Apartado.php';

$page_title = 'Apartados - ' . APP_NAME;

$apartadoModel = new Apartado();
$estado = $_GET['estado'] ?? '';
$filtros = [];
if ($estado && in_array($estado, ['activo', 'completado', 'cancelado'])) {
    $filtros['estado'] = $estado;
}

$apartados = $apartadoModel->obtenerTodos($filtros);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bookmark-star"></i> Apartados</h1>
        <a href="<?php echo BASE_URL; ?>/views/apartados/nueva.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Apartado
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="d-flex gap-2">
                <select class="form-select" name="estado" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="activo" <?php echo $estado === 'activo' ? 'selected' : ''; ?>>Activo</option>
                    <option value="completado" <?php echo $estado === 'completado' ? 'selected' : ''; ?>>Completado</option>
                    <option value="cancelado" <?php echo $estado === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </form>
        </div>
    </div>

    <?php if (empty($apartados)): ?>
        <div class="alert alert-info"><i class="bi bi-info-circle"></i> No hay apartados registrados</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>iPhone</th>
                        <th>Total</th>
                        <th>Abonado</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($apartados as $a): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($a['numero_apartado']); ?></strong></td>
                            <td><?php echo htmlspecialchars($a['cliente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($a['modelo'] . ' ' . $a['capacidad'] . ' ' . $a['color']); ?></td>
                            <td><?php echo formatearMoneda($a['monto_total']); ?></td>
                            <td><?php echo formatearMoneda($a['total_abonado']); ?></td>
                            <td><?php echo formatearMoneda($a['saldo_pendiente']); ?></td>
                            <td>
                                <?php
                                $badge = [
                                    'activo' => 'warning',
                                    'completado' => 'success',
                                    'cancelado' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?php echo $badge[$a['estado']] ?? 'secondary'; ?>">
                                    <?php echo ucfirst($a['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo formatearFechaHora($a['fecha_apartado']); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/views/apartados/ver.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
