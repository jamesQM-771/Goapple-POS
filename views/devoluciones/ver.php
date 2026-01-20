<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Devolucion.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Cliente.php';
require_once __DIR__ . '/../../models/iPhone.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/views/login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ' . BASE_URL . '/views/devoluciones/lista.php');
    exit;
}

$devolucion = new Devolucion();
$dev = $devolucion->obtenerPorId($id);

if (!$dev) {
    header('Location: ' . BASE_URL . '/views/devoluciones/lista.php');
    exit;
}

$venta = new Venta();
$ventaInfo = $venta->obtenerPorId($dev['venta_id']);
$detalleVenta = $venta->obtenerDetalle($dev['venta_id']);

$cliente = new Cliente();
$clienteInfo = $cliente->obtenerPorId($dev['cliente_id']);

$iphoneModel = new iPhone();
$iphoneOriginal = !empty($dev['iphone_original_id']) ? $iphoneModel->obtenerPorId($dev['iphone_original_id']) : null;
$iphoneNuevo = !empty($dev['iphone_nuevo_id']) ? $iphoneModel->obtenerPorId($dev['iphone_nuevo_id']) : null;

// Verificar si existe crédito
$creditoInfo = null;
if ($dev['tipo_venta'] === 'credito') {
    require_once __DIR__ . '/../../models/Credito.php';
    $creditoModel = new Credito();
    $creditos = $creditoModel->obtenerTodos(['venta_id' => $dev['venta_id']]);
    $creditoInfo = !empty($creditos) ? $creditos[0] : null;
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-arrow-counterclockwise"></i> Detalles de Devolución</h1>
        <a href="<?php echo BASE_URL; ?>/views/devoluciones/lista.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Lista
        </a>
    </div>

    <div class="row g-4">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información de Devolución</h5>
                    <span class="badge bg-<?php echo ($dev['estado'] === 'aprobada' ? 'success' : ($dev['estado'] === 'pendiente' ? 'warning' : ($dev['estado'] === 'rechazada' ? 'danger' : 'info'))); ?>">
                        <?php echo ucfirst($dev['estado']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Número de Devolución</label>
                            <div class="h5"><code><?php echo htmlspecialchars($dev['numero_devolucion']); ?></code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Número de Venta Original</label>
                            <div class="h5">
                                <a href="<?php echo BASE_URL; ?>/views/ventas/detalle.php?id=<?php echo $dev['venta_id']; ?>">
                                    <?php echo htmlspecialchars($ventaInfo['numero_venta'] ?? 'N/A'); ?>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">Cliente</label>
                            <div class="h5">
                                <a href="<?php echo BASE_URL; ?>/views/clientes/ver.php?id=<?php echo $dev['cliente_id']; ?>">
                                    <?php echo htmlspecialchars($clienteInfo['nombre'] ?? 'N/A'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Teléfono</label>
                            <div class="h5"><?php echo htmlspecialchars($clienteInfo['telefono'] ?? 'N/A'); ?></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small">Motivo de Devolución</label>
                            <div class="h6"><?php echo htmlspecialchars($dev['motivo']); ?></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small">Descripción Detallada</label>
                            <div class="border bg-light p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($dev['descripcion'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financiero -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Información Financiera</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Monto de Reembolso</label>
                            <div class="h4 text-success">$<?php echo number_format($dev['monto_reembolso'], 2); ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Método de Reembolso</label>
                            <div class="h5">
                                <span class="badge bg-info">
                                    <?php 
                                    $metodos = [
                                        'efectivo' => 'Efectivo',
                                        'transferencia' => 'Transferencia Bancaria',
                                        'credito' => 'Crédito en Tienda',
                                        'devolucion_producto' => 'Devolución del Producto'
                                    ];
                                    echo $metodos[$dev['metodo_reembolso']] ?? ucfirst($dev['metodo_reembolso']);
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tipo de Solicitud</label>
                            <div class="h5">
                                <span class="badge bg-<?php echo ($dev['tipo_solicitud'] ?? 'devolucion') === 'cambio' ? 'warning' : 'primary'; ?>">
                                    <?php echo strtoupper($dev['tipo_solicitud'] ?? 'DEVOLUCION'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tipo de Venta</label>
                            <div class="h5">
                                <span class="badge bg-<?php echo ($dev['tipo_venta'] ?? '') === 'credito' ? 'warning' : 'success'; ?>">
                                    <?php echo strtoupper($dev['tipo_venta'] ?? 'N/A'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Estado de Venta</label>
                            <div class="h5">
                                <span class="badge bg-<?php echo ($dev['venta_estado'] ?? '') === 'cancelada' ? 'danger' : 'success'; ?>">
                                    <?php echo strtoupper($dev['venta_estado'] ?? 'N/A'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (($dev['tipo_solicitud'] ?? 'devolucion') === 'cambio'): ?>
                <!-- Cambio de equipo -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Detalle de Cambio</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">iPhone Devuelto</label>
                                <?php if ($iphoneOriginal): ?>
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?php echo htmlspecialchars($iphoneOriginal['modelo']); ?></strong><br>
                                        <?php echo htmlspecialchars($iphoneOriginal['capacidad']); ?>GB - <?php echo htmlspecialchars($iphoneOriginal['color']); ?><br>
                                        IMEI: <?php echo htmlspecialchars($iphoneOriginal['imei']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted">No registrado</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">iPhone Nuevo</label>
                                <?php if ($iphoneNuevo): ?>
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?php echo htmlspecialchars($iphoneNuevo['modelo']); ?></strong><br>
                                        <?php echo htmlspecialchars($iphoneNuevo['capacidad']); ?>GB - <?php echo htmlspecialchars($iphoneNuevo['color']); ?><br>
                                        IMEI: <?php echo htmlspecialchars($iphoneNuevo['imei']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted">No registrado</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- iPhones Devueltos -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-phone"></i> iPhones Devueltos</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($detalleVenta)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Modelo</th>
                                        <th>IMEI</th>
                                        <th>Capacidad</th>
                                        <th>Color</th>
                                        <th>Condición</th>
                                        <th class="text-end">Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalDevuelto = 0;
                                    foreach ($detalleVenta as $item): 
                                        $totalDevuelto += (float)($item['precio_unitario'] ?? 0);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($item['modelo']); ?></strong></td>
                                        <td><code><?php echo htmlspecialchars($item['imei']); ?></code></td>
                                        <td><?php echo htmlspecialchars($item['capacidad']); ?> GB</td>
                                        <td><?php echo htmlspecialchars($item['color']); ?></td>
                                        <td><?php echo htmlspecialchars($item['condicion']); ?></td>
                                        <td class="text-end">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">Total:</th>
                                        <th class="text-end">$<?php echo number_format($totalDevuelto, 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">No se encontraron iPhones asociados a esta venta.</div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($creditoInfo): ?>
                <!-- Ajuste de Crédito -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Ajuste de Crédito</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Monto Financiado</label>
                                <div class="h5">$<?php echo number_format($creditoInfo['monto_financiado'], 2); ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Saldo Pendiente Actual</label>
                                <div class="h5 text-primary">$<?php echo number_format($creditoInfo['saldo_pendiente'], 2); ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Reembolso Aplicado</label>
                                <div class="h5 text-danger">-$<?php echo number_format($dev['monto_reembolso'], 2); ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Estado del Crédito</label>
                                <div class="h5">
                                    <span class="badge bg-<?php echo ($creditoInfo['estado'] === 'pagado') ? 'info' : (($creditoInfo['estado'] === 'mora') ? 'danger' : 'success'); ?>">
                                        <?php echo strtoupper($creditoInfo['estado']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if ($dev['estado'] === 'completada'): ?>
                            <div class="alert alert-info mt-3 mb-0">
                                El saldo del crédito fue ajustado automáticamente al completar la devolución.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Timeline y Acciones -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historial</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Solicitud -->
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <strong>Solicitud Creada</strong>
                                <div class="text-muted small">
                                    <?php echo date('d/m/Y H:i', strtotime($dev['fecha_solicitud'])); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Aprobación/Rechazo -->
                        <?php if ($dev['estado'] !== 'pendiente'): ?>
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-<?php echo $dev['estado'] === 'aprobada' ? 'success' : 'danger'; ?>"></div>
                                <div class="timeline-content">
                                    <strong><?php echo ucfirst($dev['estado']); ?> por Admin</strong>
                                    <div class="text-muted small">
                                        <?php echo date('d/m/Y H:i', strtotime($dev['fecha_aprobacion'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Completada -->
                        <?php if ($dev['estado'] === 'completada'): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <strong>Devolución Completada</strong>
                                    <div class="text-muted small">Reembolso procesado</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Notas</h5>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded" style="min-height: 100px;">
                        <?php echo $dev['notas'] ? nl2br(htmlspecialchars($dev['notas'])) : '<em class="text-muted">Sin notas</em>'; ?>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <?php if ($dev['estado'] === 'pendiente' && esAdmin()): ?>
                <div class="mt-4">
                    <button type="button" class="btn btn-success w-100 mb-2" id="btnAprobar">
                        <i class="bi bi-check-circle"></i> Aprobar Devolución
                    </button>
                    <button type="button" class="btn btn-danger w-100" id="btnRechazar">
                        <i class="bi bi-x-circle"></i> Rechazar Devolución
                    </button>
                </div>
            <?php elseif ($dev['estado'] === 'aprobada' && esAdmin()): ?>
                <div class="mt-4">
                    <button type="button" class="btn btn-primary w-100" id="btnCompletar">
                        <i class="bi bi-check2-all"></i> Marcar como Completada
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-left: 15px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -18px;
    top: 25px;
    width: 2px;
    height: calc(100% + 10px);
    background: #e5e7eb;
}

.timeline-marker {
    position: absolute;
    left: -27px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content strong {
    display: block;
    margin-bottom: 5px;
}
</style>

<script>
document.getElementById('btnAprobar')?.addEventListener('click', function() {
    Swal.fire({
        title: '¿Aprobar esta devolución?',
        text: 'La solicitud será aprobada y se procesará el reembolso',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, Aprobar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('<?php echo BASE_URL; ?>/controllers/api.php?module=devoluciones&action=aprobar', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: <?php echo $id; ?>})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Aprobada!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
});

document.getElementById('btnRechazar')?.addEventListener('click', function() {
    Swal.fire({
        title: '¿Rechazar esta devolución?',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo',
        inputPlaceholder: 'Escribe el motivo...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, Rechazar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('<?php echo BASE_URL; ?>/controllers/api.php?module=devoluciones&action=rechazar', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: <?php echo $id; ?>, motivo: result.value || ''})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Rechazada!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
});

document.getElementById('btnCompletar')?.addEventListener('click', function() {
    Swal.fire({
        title: '¿Marcar como completada?',
        text: 'El reembolso ha sido procesado exitosamente',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, Completar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('<?php echo BASE_URL; ?>/controllers/api.php?module=devoluciones&action=completar', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: <?php echo $id; ?>})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Completada!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
