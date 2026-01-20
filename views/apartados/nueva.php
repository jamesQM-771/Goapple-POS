<?php
/**
 * Nuevo Apartado
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Apartado.php';
require_once __DIR__ . '/../../models/iPhone.php';
require_once __DIR__ . '/../../models/Cliente.php';

$page_title = 'Nuevo Apartado - ' . APP_NAME;

$apartadoModel = new Apartado();
$iphoneModel = new iPhone();
$clienteModel = new Cliente();

$iphones = $iphoneModel->obtenerDisponibles();
$clientes = $clienteModel->obtenerTodos();

$errores = [];
$valores = [
    'cliente_id' => '',
    'iphone_id' => '',
    'fecha_limite' => '',
    'abono_inicial' => '0',
    'forma_pago' => 'efectivo',
    'observaciones' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($valores as $k => $v) {
        $valores[$k] = sanitizar($_POST[$k] ?? $v);
    }

    if (empty($valores['cliente_id'])) $errores[] = 'Debe seleccionar un cliente';
    if (empty($valores['iphone_id'])) $errores[] = 'Debe seleccionar un iPhone';

    $iphoneSeleccionado = null;
    if (!empty($valores['iphone_id'])) {
        $iphoneSeleccionado = $iphoneModel->obtenerPorId($valores['iphone_id']);
        if (!$iphoneSeleccionado) $errores[] = 'iPhone no encontrado';
        if ($iphoneSeleccionado && $iphoneSeleccionado['estado'] !== 'disponible') $errores[] = 'El iPhone no está disponible';
    }

    $abono_inicial = floatval($valores['abono_inicial']);
    if ($abono_inicial < 0) $errores[] = 'El abono inicial no puede ser negativo';
    if ($iphoneSeleccionado && $abono_inicial > floatval($iphoneSeleccionado['precio_venta'])) {
        $errores[] = 'El abono inicial no puede superar el total';
    }

    if (empty($errores)) {
        $datos = [
            'cliente_id' => intval($valores['cliente_id']),
            'iphone_id' => intval($valores['iphone_id']),
            'vendedor_id' => usuarioActual()['id'],
            'fecha_limite' => $valores['fecha_limite'] ?: null,
            'monto_total' => $iphoneSeleccionado['precio_venta'],
            'abono_inicial' => $abono_inicial,
            'forma_pago' => $valores['forma_pago'],
            'observaciones' => $valores['observaciones']
        ];

        $id = $apartadoModel->crear($datos);
        if ($id) {
            setFlashMessage('success', 'Apartado creado correctamente');
            redirect('/views/apartados/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo crear el apartado';
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bookmark-star"></i> Nuevo Apartado</h1>
        <a href="<?php echo BASE_URL; ?>/views/apartados/lista.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

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
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <select class="form-select select2" name="cliente_id" required>
                        <option value="">-- Seleccionar cliente --</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>" <?php echo (string)$valores['cliente_id'] === (string)$cliente['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cliente['nombre']); ?> - <?php echo htmlspecialchars($cliente['cedula']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">iPhone</label>
                    <select class="form-select select2" name="iphone_id" required>
                        <option value="">-- Seleccionar iPhone --</option>
                        <?php foreach ($iphones as $iphone): ?>
                            <option value="<?php echo $iphone['id']; ?>" <?php echo (string)$valores['iphone_id'] === (string)$iphone['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($iphone['modelo'] . ' ' . $iphone['capacidad'] . ' ' . $iphone['color']); ?> - <?php echo formatearMoneda($iphone['precio_venta']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha Límite (opcional)</label>
                    <input type="date" class="form-control" name="fecha_limite" value="<?php echo htmlspecialchars($valores['fecha_limite']); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Abono Inicial</label>
                    <input type="number" class="form-control" name="abono_inicial" min="0" step="0.01" value="<?php echo htmlspecialchars($valores['abono_inicial']); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Forma de Pago</label>
                    <select class="form-select" name="forma_pago">
                        <option value="efectivo" <?php echo $valores['forma_pago'] === 'efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                        <option value="transferencia" <?php echo $valores['forma_pago'] === 'transferencia' ? 'selected' : ''; ?>>Transferencia</option>
                        <option value="tarjeta" <?php echo $valores['forma_pago'] === 'tarjeta' ? 'selected' : ''; ?>>Tarjeta</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" name="observaciones" rows="3"><?php echo htmlspecialchars($valores['observaciones']); ?></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Crear Apartado
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/apartados/lista.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
