<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Devolucion.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Cliente.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../models/iPhone.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/views/login.php');
    exit;
}

$errores = [];
$mensaje = '';
$devolucion = new Devolucion();
$venta = new Venta();
$iphoneModel = new iPhone();

// Obtener todas las ventas para el formulario
$ventas = $venta->obtenerTodos();
$iphonesDisponibles = $iphoneModel->obtenerTodos(['estado' => 'disponible']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos
    $venta_id = $_POST['venta_id'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $monto_reembolso = $_POST['monto_reembolso'] ?? '';
    $metodo_reembolso = $_POST['metodo_reembolso'] ?? '';
    $tipo_solicitud = $_POST['tipo_solicitud'] ?? 'devolucion';
    $iphone_original_id = $_POST['iphone_original_id'] ?? null;
    $iphone_nuevo_id = $_POST['iphone_nuevo_id'] ?? null;

    // Validaciones
    if (empty($venta_id)) $errores[] = 'Debe seleccionar una venta';
    if (empty($motivo)) $errores[] = 'El motivo es requerido';
    if (empty($descripcion)) $errores[] = 'La descripción es requerida';
    if ($tipo_solicitud === 'devolucion') {
        if (empty($monto_reembolso) || !is_numeric($monto_reembolso) || $monto_reembolso <= 0) {
            $errores[] = 'El monto de reembolso debe ser un número válido';
        }
    } else {
        if (!is_numeric($monto_reembolso) || $monto_reembolso < 0) {
            $errores[] = 'El monto de reembolso debe ser un número válido';
        }
    }
    if (!in_array($metodo_reembolso, ['efectivo', 'transferencia', 'credito', 'devolucion_producto'])) {
        $errores[] = 'Método de reembolso inválido';
    }
    if (!in_array($tipo_solicitud, ['devolucion', 'cambio'])) {
        $errores[] = 'Tipo de solicitud inválido';
    }
    if ($tipo_solicitud === 'cambio') {
        if ($metodo_reembolso !== 'devolucion_producto') {
            $errores[] = 'Para cambios, el método debe ser "Devolución del Producto"';
        }
        if (empty($iphone_original_id) || empty($iphone_nuevo_id)) {
            $errores[] = 'Debes seleccionar el iPhone devuelto y el iPhone nuevo para el cambio';
        }
    }

    // Verificar que la venta existe
    $ventaExistente = $venta->obtenerPorId($venta_id);
    if (!$ventaExistente) {
        $errores[] = 'La venta seleccionada no existe';
    }

    if (empty($errores)) {
        try {
            $datos = [
                'venta_id' => $venta_id,
                'cliente_id' => $ventaExistente['cliente_id'],
                'motivo' => $motivo,
                'descripcion' => $descripcion,
                'tipo_solicitud' => $tipo_solicitud,
                'iphone_original_id' => $iphone_original_id ?: null,
                'iphone_nuevo_id' => $iphone_nuevo_id ?: null,
                'monto_reembolso' => $monto_reembolso,
                'metodo_reembolso' => $metodo_reembolso,
                'usuario_solicita' => $_SESSION['usuario_id']
            ];

            if ($devolucion->crear($datos)) {
                $mensaje = 'Devolución creada exitosamente. Espera la aprobación del administrador.';
                // Limpiar formulario
                $_POST = [];
            } else {
                $errores[] = 'Error al crear la devolución';
            }
        } catch (Exception $e) {
            $errores[] = 'Error: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-plus-circle"></i> Nueva Devolución</h1>
        <a href="<?php echo BASE_URL; ?>/views/devoluciones/lista.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Lista
        </a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> <strong>Errores encontrados:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errores as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Información de Devolución</h5>
                </div>
                <div class="card-body">
                    <form method="POST" novalidate>
                        <!-- Selección de Venta -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Venta Original <span class="text-danger">*</span></label>
                            <select class="form-select" name="venta_id" id="ventaSelect" required onchange="cargarVenta()">
                                <option value="">-- Selecciona una venta --</option>
                                <?php foreach ($ventas as $v): ?>
                                    <option value="<?php echo $v['id']; ?>" 
                                            data-cliente="<?php echo htmlspecialchars($v['cliente_nombre'] ?? ''); ?>"
                                            data-total="<?php echo $v['total']; ?>"
                                            <?php echo (isset($_POST['venta_id']) && $_POST['venta_id'] == $v['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($v['numero_venta']); ?> - 
                                        <?php echo htmlspecialchars($v['cliente_nombre'] ?? 'Cliente'); ?> - 
                                        $<?php echo number_format($v['total'], 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Selecciona la venta para la cual deseas crear la devolución</small>
                        </div>

                        <!-- Info de Venta Seleccionada -->
                        <div class="row g-3 mb-4" id="ventaInfo" style="display: none;">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <small class="text-muted d-block">Cliente</small>
                                    <strong id="clienteInfo">-</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <small class="text-muted d-block">Monto Total Original</small>
                                    <strong id="montoOriginal">$0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de Solicitud -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Solicitud <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipo_solicitud" id="tipoSolicitud" required>
                                <option value="devolucion" <?php echo (isset($_POST['tipo_solicitud']) && $_POST['tipo_solicitud'] === 'devolucion') ? 'selected' : ''; ?>>
                                    Devolución (reembolso)
                                </option>
                                <option value="cambio" <?php echo (isset($_POST['tipo_solicitud']) && $_POST['tipo_solicitud'] === 'cambio') ? 'selected' : ''; ?>>
                                    Cambio (intercambio de equipo)
                                </option>
                            </select>
                            <small class="text-muted">Selecciona si es devolución completa o cambio de equipo</small>
                        </div>

                        <!-- Cambio de equipo -->
                        <div class="mb-4" id="cambioBlock" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">iPhone Devuelto <span class="text-danger">*</span></label>
                                    <select class="form-select" name="iphone_original_id" id="iphoneOriginalSelect">
                                        <option value="">-- Selecciona un iPhone de la venta --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">iPhone Nuevo <span class="text-danger">*</span></label>
                                    <select class="form-select" name="iphone_nuevo_id">
                                        <option value="">-- Selecciona iPhone disponible --</option>
                                        <?php foreach ($iphonesDisponibles as $ip): ?>
                                            <option value="<?php echo $ip['id']; ?>" <?php echo (isset($_POST['iphone_nuevo_id']) && $_POST['iphone_nuevo_id'] == $ip['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ip['modelo']); ?> - <?php echo htmlspecialchars($ip['capacidad']); ?>GB - <?php echo htmlspecialchars($ip['color']); ?> (<?php echo htmlspecialchars($ip['imei']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">El iPhone original se devolverá a inventario y el nuevo quedará asociado a la venta.</small>
                        </div>

                        <!-- Motivo de Devolución -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Motivo de Devolución <span class="text-danger">*</span></label>
                            <select class="form-select" name="motivo" required>
                                <option value="">-- Selecciona un motivo --</option>
                                <option value="Producto Defectuoso" <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'Producto Defectuoso') ? 'selected' : ''; ?>>
                                    Producto Defectuoso
                                </option>
                                <option value="Producto No Corresponde" <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'Producto No Corresponde') ? 'selected' : ''; ?>>
                                    Producto No Corresponde
                                </option>
                                <option value="Insatisfacción del Cliente" <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'Insatisfacción del Cliente') ? 'selected' : ''; ?>>
                                    Insatisfacción del Cliente
                                </option>
                                <option value="Cambio de Decisión" <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'Cambio de Decisión') ? 'selected' : ''; ?>>
                                    Cambio de Decisión
                                </option>
                                <option value="Error en Venta" <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'Error en Venta') ? 'selected' : ''; ?>>
                                    Error en Venta
                                </option>
                                <option value="Otro" <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'Otro') ? 'selected' : ''; ?>>
                                    Otro
                                </option>
                            </select>
                        </div>

                        <!-- Descripción Detallada -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción Detallada <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="descripcion" rows="5" required 
                                      placeholder="Describe en detalle el motivo de la devolución..."><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                            <small class="text-muted">Proporciona todos los detalles relevantes para evaluar la solicitud</small>
                        </div>

                        <!-- Monto a Reembolsar -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Monto de Reembolso <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="monto_reembolso" 
                                       step="0.01" min="0" required
                                       value="<?php echo htmlspecialchars($_POST['monto_reembolso'] ?? ''); ?>"
                                       placeholder="0.00">
                            </div>
                            <small class="text-muted">El monto que se reembolsará al cliente</small>
                        </div>

                        <!-- Método de Reembolso -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Método de Reembolso <span class="text-danger">*</span></label>
                            <select class="form-select" name="metodo_reembolso" required>
                                <option value="">-- Selecciona un método --</option>
                                <option value="efectivo" <?php echo (isset($_POST['metodo_reembolso']) && $_POST['metodo_reembolso'] === 'efectivo') ? 'selected' : ''; ?>>
                                    Efectivo
                                </option>
                                <option value="transferencia" <?php echo (isset($_POST['metodo_reembolso']) && $_POST['metodo_reembolso'] === 'transferencia') ? 'selected' : ''; ?>>
                                    Transferencia Bancaria
                                </option>
                                <option value="credito" <?php echo (isset($_POST['metodo_reembolso']) && $_POST['metodo_reembolso'] === 'credito') ? 'selected' : ''; ?>>
                                    Crédito en Tienda
                                </option>
                                <option value="devolucion_producto" <?php echo (isset($_POST['metodo_reembolso']) && $_POST['metodo_reembolso'] === 'devolucion_producto') ? 'selected' : ''; ?>>
                                    Devolución del Producto
                                </option>
                            </select>
                            <small class="text-muted">Cómo se reembolsará el dinero al cliente</small>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Crear Devolución
                            </button>
                            <a href="<?php echo BASE_URL; ?>/views/devoluciones/lista.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información</h5>
                </div>
                <div class="card-body small">
                    <p><strong>Proceso de Devolución:</strong></p>
                    <ol class="ps-3 mb-0">
                        <li>Completa todos los campos requeridos</li>
                        <li>Selecciona la venta original</li>
                        <li>Describe el motivo en detalle</li>
                        <li>Indica el monto y método de reembolso</li>
                        <li>Envía la solicitud para aprobación</li>
                        <li>Espera a que el administrador apruebe o rechace</li>
                    </ol>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-lightbulb"></i>
                <strong>Nota:</strong> La solicitud será revisada por el administrador y se notificará al cliente del resultado.
            </div>
        </div>
    </div>
</div>

<script>
function cargarVenta() {
    const select = document.getElementById('ventaSelect');
    const option = select.options[select.selectedIndex];
    const ventaInfo = document.getElementById('ventaInfo');
    const iphoneOriginalSelect = document.getElementById('iphoneOriginalSelect');
    
    if (option.value) {
        document.getElementById('clienteInfo').textContent = option.dataset.cliente;
        document.getElementById('montoOriginal').textContent = '$' + parseFloat(option.dataset.total).toFixed(2);
        document.querySelector('input[name="monto_reembolso"]').value = option.dataset.total;
        ventaInfo.style.display = 'grid';

        fetch(`<?php echo BASE_URL; ?>/controllers/api.php?module=ventas&action=detalle&id=${option.value}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    iphoneOriginalSelect.innerHTML = '<option value="">-- Selecciona un iPhone de la venta --</option>';
                    data.data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.iphone_id;
                        opt.textContent = `${item.modelo} - ${item.capacidad}GB - ${item.color} (${item.imei})`;
                        if (String(<?php echo json_encode($_POST['iphone_original_id'] ?? ''); ?>) === String(item.iphone_id)) {
                            opt.selected = true;
                        }
                        iphoneOriginalSelect.appendChild(opt);
                    });
                }
            });
    } else {
        ventaInfo.style.display = 'none';
        iphoneOriginalSelect.innerHTML = '<option value="">-- Selecciona un iPhone de la venta --</option>';
    }
}

function toggleCambio() {
    const tipo = document.getElementById('tipoSolicitud').value;
    const cambioBlock = document.getElementById('cambioBlock');
    const metodo = document.querySelector('select[name="metodo_reembolso"]');
    const monto = document.querySelector('input[name="monto_reembolso"]');

    if (tipo === 'cambio') {
        cambioBlock.style.display = 'block';
        metodo.value = 'devolucion_producto';
        monto.value = monto.value && parseFloat(monto.value) > 0 ? monto.value : 0;
    } else {
        cambioBlock.style.display = 'none';
    }
}

// Cargar venta si hay una seleccionada
window.addEventListener('load', cargarVenta);
document.getElementById('tipoSolicitud').addEventListener('change', toggleCambio);
window.addEventListener('load', toggleCambio);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
