<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Devolucion.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/views/login.php');
    exit;
}

// Obtener parámetros de filtro
$estado = $_GET['estado'] ?? '';
$filtros = [];
if ($estado && in_array($estado, ['pendiente', 'aprobada', 'rechazada', 'completada'])) {
    $filtros['estado'] = $estado;
}

$devolucion = new Devolucion();
$devoluciones = $devolucion->obtenerTodas($filtros);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-arrow-counterclockwise"></i> Gestión de Devoluciones</h1>
        <?php if (esAdmin()): ?>
            <a href="<?php echo BASE_URL; ?>/views/devoluciones/nueva.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Devolución
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-auto">
                    <form class="d-flex gap-2" method="GET">
                        <select class="form-select" name="estado" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?php echo $estado === 'pendiente' ? 'selected' : ''; ?>>
                                Pendiente de Aprobación
                            </option>
                            <option value="aprobada" <?php echo $estado === 'aprobada' ? 'selected' : ''; ?>>
                                Aprobada
                            </option>
                            <option value="rechazada" <?php echo $estado === 'rechazada' ? 'selected' : ''; ?>>
                                Rechazada
                            </option>
                            <option value="completada" <?php echo $estado === 'completada' ? 'selected' : ''; ?>>
                                Completada
                            </option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($devoluciones)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No hay devoluciones registradas
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Número Devolución</th>
                        <th>Cliente</th>
                        <th>Venta Original</th>
                        <th>Motivo</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha Solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($devoluciones as $dev): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($dev['numero_devolucion']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($dev['cliente_nombre'] ?? 'N/A'); ?></td>
                            <td>
                                <code><?php echo htmlspecialchars($dev['numero_venta'] ?? 'N/A'); ?></code>
                            </td>
                            <td><?php echo htmlspecialchars($dev['motivo']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo ($dev['tipo_solicitud'] ?? 'devolucion') === 'cambio' ? 'warning' : 'primary'; ?>">
                                    <?php echo strtoupper($dev['tipo_solicitud'] ?? 'DEVOLUCION'); ?>
                                </span>
                            </td>
                            <td>
                                <strong>$<?php echo number_format($dev['monto_reembolso'], 2); ?></strong>
                            </td>
                            <td>
                                <?php 
                                $estado_badge = [
                                    'pendiente' => 'warning',
                                    'aprobada' => 'success',
                                    'rechazada' => 'danger',
                                    'completada' => 'info'
                                ];
                                $estado_icon = [
                                    'pendiente' => 'hourglass-split',
                                    'aprobada' => 'check-circle',
                                    'rechazada' => 'x-circle',
                                    'completada' => 'clipboard-check'
                                ];
                                $badge_class = $estado_badge[$dev['estado']] ?? 'secondary';
                                $icon = $estado_icon[$dev['estado']] ?? 'question-circle';
                                ?>
                                <span class="badge bg-<?php echo $badge_class; ?>">
                                    <i class="bi bi-<?php echo $icon; ?>"></i>
                                    <?php echo ucfirst($dev['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($dev['fecha_solicitud'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo BASE_URL; ?>/views/devoluciones/ver.php?id=<?php echo $dev['id']; ?>" 
                                       class="btn btn-outline-info" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($dev['estado'] === 'pendiente' && esAdmin()): ?>
                                        <button type="button" class="btn btn-outline-success btn-aprobar" 
                                                data-id="<?php echo $dev['id']; ?>" title="Aprobar devolución">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-rechazar" 
                                                data-id="<?php echo $dev['id']; ?>" title="Rechazar devolución">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($dev['estado'] === 'aprobada' && esAdmin()): ?>
                                        <button type="button" class="btn btn-outline-primary btn-completar" 
                                                data-id="<?php echo $dev['id']; ?>" title="Marcar como completada">
                                            <i class="bi bi-check2-all"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// Aprobar devolución
document.querySelectorAll('.btn-aprobar').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        Swal.fire({
            title: '¿Aprobar Devolución?',
            text: 'Esta acción aprobará la solicitud de devolución',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, Aprobar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('<?php echo BASE_URL; ?>/controllers/api.php?module=devoluciones&action=aprobar', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id: id})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Aprobada!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Error al aprobar', 'error');
                    }
                });
            }
        });
    });
});

// Rechazar devolución
document.querySelectorAll('.btn-rechazar').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        Swal.fire({
            title: '¿Rechazar Devolución?',
            input: 'textarea',
            inputLabel: 'Motivo del rechazo (opcional)',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, Rechazar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('<?php echo BASE_URL; ?>/controllers/api.php?module=devoluciones&action=rechazar', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id: id, motivo: result.value || ''})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Rechazada!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Error al rechazar', 'error');
                    }
                });
            }
        });
    });
});

// Completar devolución
document.querySelectorAll('.btn-completar').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        Swal.fire({
            title: '¿Completar Devolución?',
            text: 'Marca esta devolución como completada',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, Completar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('<?php echo BASE_URL; ?>/controllers/api.php?module=devoluciones&action=completar', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id: id})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Completada!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Error al completar', 'error');
                    }
                });
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
