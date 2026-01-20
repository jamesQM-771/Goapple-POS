<?php
/**
 * Registrar Pago de Crédito
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

$page_title = 'Registrar Pago - ' . APP_NAME;

$model = new Credito();
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'credito_id' => intval($_POST['credito_id'] ?? 0),
        'monto_pago' => floatval($_POST['monto_pago'] ?? 0),
        'numero_cuota' => intval($_POST['numero_cuota'] ?? 1),
        'forma_pago' => $_POST['forma_pago'] ?? 'efectivo',
        'usuario_id' => usuarioActual()['id'],
        'observaciones' => sanitizar($_POST['observaciones'] ?? '')
    ];

    if ($datos['credito_id'] <= 0) $errores[] = 'Seleccione un crédito';
    if ($datos['monto_pago'] <= 0) $errores[] = 'El monto del pago debe ser mayor a cero';

    if (empty($errores)) {
        $result = $model->registrarPago($datos);
        if ($result['success']) {
            setFlashMessage('success', 'Pago registrado correctamente');
            redirect('/views/creditos/ver.php?id=' . $datos['credito_id']);
        } else {
            $errores[] = $result['message'] ?? 'No se pudo registrar el pago';
        }
    }
}

$creditos = $model->obtenerTodos(['estado' => 'activo']);
$credito_preselect = intval($_GET['credito_id'] ?? 0);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h1 class="mb-4"><i class="bi bi-cash-coin"></i> Registrar Pago</h1>

            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Crédito</label>
                            <select class="form-select select2" name="credito_id" id="credito_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($creditos as $cr): ?>
                                    <option value="<?php echo $cr['id']; ?>" <?php echo $credito_preselect === intval($cr['id']) ? 'selected' : ''; ?>
                                            data-saldo="<?php echo $cr['saldo_pendiente']; ?>"
                                            data-cuota="<?php echo $cr['valor_cuota']; ?>">
                                        <?php echo $cr['numero_credito']; ?> - <?php echo htmlspecialchars($cr['cliente_nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="infoCredito" class="alert alert-info d-none"></div>

                        <div class="mb-3">
                            <label class="form-label">Monto del Pago</label>
                            <input type="number" class="form-control" name="monto_pago" id="monto_pago" min="1000" step="1000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">N° de Cuota</label>
                            <input type="number" class="form-control" name="numero_cuota" value="1" min="1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Forma de Pago</label>
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
                            <i class="bi bi-check-circle"></i> Registrar Pago
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('#credito_id').change(function() {
    const option = $(this).find(':selected');
    const saldo = option.data('saldo');
    const cuota = option.data('cuota');

    if (saldo) {
        $('#infoCredito').html(`Saldo: ${formatearMoneda(saldo)} | Cuota: ${formatearMoneda(cuota)}`).removeClass('d-none');
        $('#monto_pago').val(cuota);
    } else {
        $('#infoCredito').addClass('d-none');
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
