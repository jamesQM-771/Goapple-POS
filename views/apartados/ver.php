<?php
/**
 * Detalle de Apartado
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Apartado.php';

$page_title = 'Detalle de Apartado - ' . APP_NAME;

$apartadoModel = new Apartado();
$id = intval($_GET['id'] ?? 0);
$apartado = $apartadoModel->obtenerPorId($id);

if (!$apartado) {
    setFlashMessage('error', 'Apartado no encontrado');
    redirect('/views/apartados/lista.php');
}

$errores = [];
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'pago') {
        $monto = floatval($_POST['monto'] ?? 0);
        $forma_pago = $_POST['forma_pago'] ?? 'efectivo';
        $observaciones = sanitizar($_POST['observaciones'] ?? '');

        if ($monto <= 0) {
            $errores[] = 'El monto debe ser mayor a 0';
        }
        if ($monto > floatval($apartado['saldo_pendiente'])) {
            $errores[] = 'El monto excede el saldo pendiente';
        }

        if (empty($errores)) {
            $ok = $apartadoModel->registrarPago($id, $monto, $forma_pago, usuarioActual()['id'], $observaciones);
            if ($ok) {
                $resultado = $apartadoModel->completarSiAplicable($id, usuarioActual()['id']);
                $exito = 'Pago registrado correctamente';
                if (!empty($resultado['success'])) {
                    $exito .= ' y apartado completado';
                }
            } else {
                $errores[] = 'Error al registrar el pago';
            }
        }
    }

    if ($accion === 'completar') {
        $resultado = $apartadoModel->completarSiAplicable($id, usuarioActual()['id']);
        if (!empty($resultado['success'])) {
            $exito = 'Apartado completado correctamente';
        } else {
            $errores[] = $resultado['message'] ?? 'No se pudo completar el apartado';
        }
    }

    if ($accion === 'cancelar') {
        $ok = $apartadoModel->cancelar($id);
        if ($ok) {
            $exito = 'Apartado cancelado';
        } else {
            $errores[] = 'No se pudo cancelar el apartado';
        }
    }

    $apartado = $apartadoModel->obtenerPorId($id);
}

$pagos = $apartadoModel->obtenerPagos($id);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bookmark-star"></i> Detalle de Apartado</h1>
        <a href="<?php echo BASE_URL; ?>/views/apartados/lista.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <?php if ($exito): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($exito); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <strong>Error:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Información del Apartado</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Número:</strong> <?php echo htmlspecialchars($apartado['numero_apartado']); ?></div>
                        <div class="col-md-6"><strong>Cliente:</strong> <?php echo htmlspecialchars($apartado['cliente_nombre']); ?></div>
                        <div class="col-md-6"><strong>iPhone:</strong> <?php echo htmlspecialchars($apartado['modelo'] . ' ' . $apartado['capacidad'] . ' ' . $apartado['color']); ?></div>
                        <div class="col-md-6"><strong>IMEI:</strong> <?php echo htmlspecialchars($apartado['imei']); ?></div>
                        <div class="col-md-6"><strong>Total:</strong> <?php echo formatearMoneda($apartado['monto_total']); ?></div>
                        <div class="col-md-6"><strong>Abonado:</strong> <?php echo formatearMoneda($apartado['total_abonado']); ?></div>
                        <div class="col-md-6"><strong>Saldo:</strong> <?php echo formatearMoneda($apartado['saldo_pendiente']); ?></div>
                        <div class="col-md-6"><strong>Estado:</strong> <?php echo ucfirst($apartado['estado']); ?></div>
                        <div class="col-md-6"><strong>Fecha:</strong> <?php echo formatearFechaHora($apartado['fecha_apartado']); ?></div>
                        <div class="col-md-6"><strong>Fecha límite:</strong> <?php echo $apartado['fecha_limite'] ?: '—'; ?></div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Historial de Abonos</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pagos)): ?>
                        <div class="alert alert-info mb-0">No hay abonos registrados</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Forma de pago</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td><?php echo formatearFechaHora($pago['fecha_pago']); ?></td>
                                            <td><?php echo formatearMoneda($pago['monto']); ?></td>
                                            <td><?php echo htmlspecialchars($pago['forma_pago']); ?></td>
                                            <td><?php echo htmlspecialchars($pago['usuario_nombre']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Registrar Abono</h5>
                </div>
                <div class="card-body">
                    <?php if ($apartado['estado'] !== 'activo'): ?>
                        <div class="alert alert-warning mb-0">El apartado no está activo.</div>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="accion" value="pago">
                            <div class="mb-3">
                                <label class="form-label">Monto</label>
                                <input type="number" class="form-control" name="monto" min="0" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Forma de pago</label>
                                <select class="form-select" name="forma_pago">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" name="observaciones" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-cash-coin"></i> Registrar Abono
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <form method="POST">
                        <input type="hidden" name="accion" value="completar">
                        <button type="submit" class="btn btn-success" <?php echo $apartado['estado'] !== 'activo' ? 'disabled' : ''; ?>>
                            <i class="bi bi-check-circle"></i> Completar Apartado
                        </button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="accion" value="cancelar">
                        <button type="submit" class="btn btn-danger" <?php echo $apartado['estado'] !== 'activo' ? 'disabled' : ''; ?>>
                            <i class="bi bi-x-circle"></i> Cancelar Apartado
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
