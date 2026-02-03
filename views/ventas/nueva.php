<?php
/**
 * Nueva Venta - Versión Simplificada
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/iPhone.php';
require_once __DIR__ . '/../../models/Cliente.php';
require_once __DIR__ . '/../../models/Credito.php';

$page_title = 'Nueva Venta - ' . APP_NAME;

$iphoneModel = new iPhone();
$clienteModel = new Cliente();
$ventaModel = new Venta();
$creditoModel = new Credito();

$iphones = $iphoneModel->obtenerDisponibles();
$clientes = $clienteModel->obtenerTodos();

$errores = [];
$exito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    // Procesar datos del formulario
    $cliente_id = intval($_POST['cliente_id'] ?? 0);
    $tipo_venta = $_POST['tipo_venta'] ?? 'contado';
    $forma_pago = $_POST['forma_pago'] ?? 'efectivo';
    $observaciones = sanitizar($_POST['observaciones'] ?? '');
    
    // Datos numéricos
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $descuento = floatval($_POST['descuento'] ?? 0);
    $total = floatval($_POST['total'] ?? 0);
    
    // Productos
    $productos_json = $_POST['productos'] ?? '[]';
    $productos = json_decode($productos_json, true) ?? [];
    
    // Validaciones
    if ($cliente_id <= 0) {
        $errores[] = 'Debe seleccionar un cliente';
    }
    
    if (empty($productos)) {
        $errores[] = 'Debe agregar al menos un iPhone';
    }
    
    if ($subtotal <= 0) {
        $errores[] = 'El subtotal debe ser mayor a 0';
    }
    
    // Si no hay errores, procesar la venta
    if (empty($errores)) {
        // Preparar datos para la venta
        $datos_venta = [
            'cliente_id' => $cliente_id,
            'vendedor_id' => usuarioActual()['id'],
            'tipo_venta' => $tipo_venta,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'total' => $total,
            'forma_pago' => $forma_pago,
            'observaciones' => $observaciones,
            'estado' => 'completada'
        ];
        
        // Agregar subtotal a cada producto
        foreach ($productos as &$producto) {
            $producto['subtotal'] = $producto['precio_unitario'] * 1; // cantidad siempre es 1
        }
        
        // Intentar crear la venta
        $resultado = $ventaModel->crear($datos_venta, $productos);
        
        if ($resultado['success']) {
            $venta_id = $resultado['venta_id'];
            $exito = true;
            
            if ($esAjax) {
                header('Content-Type: application/json; charset=utf-8');
                $baseUrl = rtrim(BASE_URL, '/') . '/';
                echo json_encode([
                    'success' => true,
                    'venta_id' => $venta_id,
                    'numero_venta' => $resultado['numero_venta'],
                    'redirect' => $baseUrl . 'views/ventas/detalle.php?id=' . $venta_id
                ]);
                exit;
            }
            setFlashMessage('success', 'Venta registrada correctamente - Venta #' . $resultado['numero_venta']);
            redirect('/views/ventas/detalle.php?id=' . $venta_id);
        } else {
            $errores[] = $resultado['message'] ?? 'Error al registrar la venta';
        }
    }

    if ($esAjax && !empty($errores)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => implode(' | ', $errores)
        ]);
        exit;
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="main-content">
    <div class="page-header mb-4">
        <h1 class="page-title mb-0">
            <i class="bi bi-cart-plus" style="color: #0071e3;"></i> Nueva Venta
        </h1>
        <p class="page-description">Registra una venta paso a paso</p>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Errores encontrados:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" id="formVenta" action="<?php echo htmlspecialchars(BASE_URL . '/views/ventas/nueva.php'); ?>">
        <!-- PASO 1: Cliente y Tipo de Venta -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #0071e3 0%, #0060c9 100%); color: white; border: none;">
                <h6 class="mb-0" style="font-weight: 600;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(255,255,255,0.3); border-radius: 50%; margin-right: 0.5rem;">1</span>
                    Seleccionar Cliente y Tipo de Venta
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Cliente <span style="color: #ff3b30;">*</span></label>
                        <select class="form-select select2" name="cliente_id" id="clienteSelect" required>
                            <option value="">-- Seleccionar cliente --</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>">
                                    <?php echo htmlspecialchars($cliente['nombre']); ?> - <?php echo htmlspecialchars($cliente['cedula']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Tipo de Venta <span style="color: #ff3b30;">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipo_venta" id="tipoContado" value="contado" checked>
                            <label class="btn btn-outline-primary" for="tipoContado" style="padding: 0.65rem;">
                                <i class="bi bi-cash-coin"></i> Contado
                            </label>
                            <input type="radio" class="btn-check" name="tipo_venta" id="tipoCredito" value="credito">
                            <label class="btn btn-outline-warning" for="tipoCredito" style="padding: 0.65rem;">
                                <i class="bi bi-calendar-check"></i> Crédito
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PASO 2: Agregar Productos -->
        <div class="card mb-4">
            <div class="card-header" style="background: white; border-bottom: 2px solid #e5e7eb;">
                <h6 class="mb-0" style="font-weight: 600; color: #111827;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #f3f4f6; border-radius: 50%; margin-right: 0.5rem; color: #0071e3;">2</span>
                    Agregar iPhones
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-lg-10">
                        <label class="form-label fw-600">Seleccionar iPhone</label>
                        <select class="form-select select2" id="seleccionarProducto">
                            <option value="">-- Seleccionar un iPhone --</option>
                            <?php foreach ($iphones as $iphone): ?>
                                <option value="<?php echo $iphone['id']; ?>"
                                        data-precio="<?php echo $iphone['precio_venta']; ?>"
                                        data-modelo="<?php echo htmlspecialchars($iphone['modelo']); ?>"
                                        data-capacidad="<?php echo htmlspecialchars($iphone['capacidad']); ?>"
                                        data-color="<?php echo htmlspecialchars($iphone['color']); ?>"
                                        data-imei="<?php echo $iphone['imei']; ?>">
                                    <?php echo htmlspecialchars($iphone['modelo'] . ' ' . $iphone['capacidad'] . ' ' . $iphone['color']); ?> - 
                                    <strong><?php echo formatearMoneda($iphone['precio_venta']); ?></strong>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-end">
                        <button type="button" class="btn btn-success w-100" id="btnAgregarProducto" style="padding: 0.65rem;">
                            <i class="bi bi-plus-circle"></i> Agregar
                        </button>
                    </div>
                </div>

                <!-- Lista de Productos -->
                <div id="contenedorProductos" style="min-height: 150px; padding: 2rem; text-align: center; border: 2px dashed #e5e7eb; border-radius: 8px;">
                    <div style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Sin iPhones seleccionados</div>
                    <div style="color: #6b7280; font-size: 0.9rem;">Selecciona iPhones de la lista arriba y haz clic en "Agregar"</div>
                </div>
            </div>
        </div>

        <!-- PASO 3: Resumen y Forma de Pago -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header" style="background: white; border-bottom: 2px solid #e5e7eb;">
                        <h6 class="mb-0" style="font-weight: 600; color: #111827;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #f3f4f6; border-radius: 50%; margin-right: 0.5rem; color: #0071e3;">3</span>
                            Detalles de Pago
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600">Forma de Pago</label>
                                <select class="form-select" name="forma_pago" id="formaPago">
                                    <option value="efectivo">💵 Efectivo</option>
                                    <option value="transferencia">🏦 Transferencia</option>
                                    <option value="tarjeta">💳 Tarjeta Débito/Crédito</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Descuento (%)</label>
                                <input type="number" class="form-control" id="porcentajeDescuento" value="0" min="0" max="100" step="0.1">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-600">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="3" placeholder="Notas adicionales de la venta..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Fotos de la Venta (Opcional) -->
                <div class="mt-4">
                    <?php 
                    if (class_exists('Foto')) {
                        $id_zona = 'venta';
                        $tipo_fotos = 'de la Venta';
                        include __DIR__ . '/../components/fotos-upload.php'; 
                    }
                    ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card" style="border: 2px solid #0071e3;">
                    <div class="card-header" style="background: linear-gradient(135deg, #0071e3 0%, #34c759 100%); color: white; border: none;">
                        <h6 class="mb-0" style="font-weight: 600;">
                            <i class="bi bi-receipt"></i> Resumen Total
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small style="color: #6b7280;">Subtotal</small>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #111827;">
                                <span id="displaySubtotal">$0</span>
                            </div>
                        </div>
                        <hr style="border-color: #e5e7eb; margin: 1rem 0;">
                        <div class="mb-3">
                            <small style="color: #6b7280;">Descuento</small>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #ff9500;">
                                <span id="displayDescuento">$0</span>
                            </div>
                        </div>
                        <hr style="border-color: #e5e7eb; margin: 1rem 0;">
                        <div class="mb-4">
                            <small style="color: #6b7280;">Total a Pagar</small>
                            <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">
                                <span id="displayTotal">$0</span>
                            </div>
                        </div>

                        <!-- Inputs Ocultos para POST -->
                        <input type="hidden" name="subtotal" id="postSubtotal" value="0">
                        <input type="hidden" name="descuento" id="postDescuento" value="0">
                        <input type="hidden" name="total" id="postTotal" value="0">
                        <input type="hidden" name="productos" id="postProductos" value="[]">

                        <button type="submit" class="btn btn-primary w-100" style="padding: 0.8rem; font-weight: 600;">
                            <i class="bi bi-check-circle"></i> Completar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN CRÉDITO -->
        <div id="seccionCredito" style="display: none; margin-top: 2rem;">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header" style="background: linear-gradient(135deg, #ff9500 0%, #ff6b35 100%); color: white; border: none;">
                            <h6 class="mb-0" style="font-weight: 600;">
                                <i class="bi bi-percent"></i> Parámetros del Crédito
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-600">Cuota Inicial ($)</label>
                                <input type="number" class="form-control" name="cuota_inicial" id="cuota_inicial" value="0" min="0" step="1000">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600">Tasa de Interés Mensual (%)</label>
                                <input type="number" class="form-control" name="tasa_interes" id="tasa_interes" value="<?php echo TASA_INTERES_DEFAULT; ?>" step="0.1" min="0" max="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600">Número de Cuotas</label>
                                <input type="number" class="form-control" name="numero_cuotas" id="numero_cuotas" value="12" min="1" max="60">
                            </div>
                            <button type="button" class="btn btn-success w-100" id="btnCalcularCredito">
                                <i class="bi bi-calculator"></i> Calcular Crédito
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header" style="background: linear-gradient(135deg, #34c759 0%, #30b0c0 100%); color: white; border: none;">
                            <h6 class="mb-0" style="font-weight: 600;">
                                <i class="bi bi-bar-chart"></i> Detalles del Crédito
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="resultadoCredito" style="text-align: center; padding: 2rem 1rem; color: #6b7280;">
                                Ingresa los parámetros y haz clic en "Calcular"
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let productos = [];

// Mostrar/ocultar sección crédito
document.querySelectorAll('input[name="tipo_venta"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('seccionCredito').style.display = this.value === 'credito' ? 'block' : 'none';
    });
});

// Botón agregar producto
document.getElementById('btnAgregarProducto').addEventListener('click', function(e) {
    e.preventDefault();
    const select = document.getElementById('seleccionarProducto');
    const id = select.value;
    
    if (!id) {
        alert('Selecciona un iPhone');
        return;
    }
    
    const option = select.options[select.selectedIndex];
    const producto = {
        iphone_id: parseInt(id),
        modelo: option.getAttribute('data-modelo'),
        capacidad: option.getAttribute('data-capacidad'),
        color: option.getAttribute('data-color'),
        imei: option.getAttribute('data-imei'),
        precio_unitario: parseFloat(option.getAttribute('data-precio'))
    };
    
    productos.push(producto);
    actualizarTablaProductos();
    actualizarTotales();
    select.value = '';
});

// Actualizar tabla de productos
function actualizarTablaProductos() {
    const contenedor = document.getElementById('contenedorProductos');
    
    if (productos.length === 0) {
        contenedor.innerHTML = `<div style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"><i class="bi bi-inbox"></i></div><div style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Sin iPhones seleccionados</div><div style="color: #6b7280; font-size: 0.9rem;">Selecciona iPhones de la lista arriba</div>`;
        return;
    }
    
    let html = '<table class="table table-hover" style="margin-bottom: 0;"><thead style="background: #f3f4f6;"><tr><th>Modelo</th><th>Capacidad</th><th>Color</th><th>IMEI</th><th>Precio</th><th>Acción</th></tr></thead><tbody>';
    
    productos.forEach((p, i) => {
        html += `<tr>
            <td style="font-weight: 600;">${p.modelo}</td>
            <td>${p.capacidad}</td>
            <td>${p.color}</td>
            <td><code>${p.imei}</code></td>
            <td style="color: #0071e3; font-weight: 600;">$${p.precio_unitario.toLocaleString('es-CO')}</td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${i})"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    });
    
    html += '</tbody></table>';
    contenedor.innerHTML = html;
}

function eliminarProducto(index) {
    productos.splice(index, 1);
    actualizarTablaProductos();
    actualizarTotales();
}

// Actualizar totales
function actualizarTotales() {
    const subtotal = productos.reduce((s, p) => s + p.precio_unitario, 0);
    const descuentoInput = document.getElementById('porcentajeDescuento');
    const porcentaje = parseFloat(descuentoInput.value) || 0;
    const descuento = (subtotal * porcentaje) / 100;
    const total = subtotal - descuento;
    
    // Mostrar
    document.getElementById('displaySubtotal').textContent = '$' + subtotal.toLocaleString('es-CO');
    document.getElementById('displayDescuento').textContent = '$' + descuento.toLocaleString('es-CO');
    document.getElementById('displayTotal').textContent = '$' + total.toLocaleString('es-CO');
    
    // POST
    document.getElementById('postSubtotal').value = subtotal;
    document.getElementById('postDescuento').value = descuento;
    document.getElementById('postTotal').value = total;
    document.getElementById('postProductos').value = JSON.stringify(productos);
}

document.getElementById('porcentajeDescuento').addEventListener('input', actualizarTotales);

// Calcular crédito
document.getElementById('btnCalcularCredito').addEventListener('click', function(e) {
    e.preventDefault();
    
    const total = parseFloat(document.getElementById('postTotal').value) || 0;
    const cuotaInicial = parseFloat(document.getElementById('cuota_inicial').value) || 0;
    const tasa = parseFloat(document.getElementById('tasa_interes').value) || 2.5;
    const cuotas = parseInt(document.getElementById('numero_cuotas').value) || 12;
    
    if (total <= 0) {
        alert('Agrega productos primero');
        return;
    }
    
    const montoFinanciado = total - cuotaInicial;
    if (montoFinanciado <= 0) {
        document.getElementById('resultadoCredito').innerHTML = '<div style="color: #34c759;"><i class="bi bi-check-circle" style="font-size: 2rem;"></i></div><div style="color: #111827; font-weight: 600;">100% Contado</div>';
        return;
    }
    
    // Cálculo
    const t = tasa / 100;
    const divisor = Math.pow(1 + t, cuotas) - 1;
    const cuota = (montoFinanciado * t * Math.pow(1 + t, cuotas)) / divisor;
    const totalAPagar = cuotaInicial + (cuota * cuotas);
    const intereses = totalAPagar - total;
    
    document.getElementById('resultadoCredito').innerHTML = `
        <div style="text-align: left;">
            <div class="mb-2"><small style="color: #6b7280;">Cuota Inicial</small><div style="font-weight: 700;">$${cuotaInicial.toLocaleString('es-CO')}</div></div>
            <hr style="border-color: #e5e7eb;">
            <div class="mb-2"><small style="color: #6b7280;">Monto Financiado</small><div style="font-weight: 700;">$${montoFinanciado.toLocaleString('es-CO')}</div></div>
            <hr style="border-color: #e5e7eb;">
            <div class="mb-2"><small style="color: #6b7280;">Cuota Mensual (${cuotas})</small><div style="font-weight: 700; color: #0071e3;">$${cuota.toLocaleString('es-CO')}</div></div>
            <hr style="border-color: #e5e7eb;">
            <div class="mb-2"><small style="color: #6b7280;">Total Intereses</small><div style="font-weight: 700; color: #ff9500;">$${intereses.toLocaleString('es-CO')}</div></div>
            <hr style="border-color: #e5e7eb;">
            <div><small style="color: #6b7280;">Total a Pagar</small><div style="font-weight: 700; color: #34c759; font-size: 1.2rem;">$${totalAPagar.toLocaleString('es-CO')}</div></div>
        </div>
    `;
});

// Submit form
document.getElementById('formVenta').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const clienteId = document.querySelector('[name="cliente_id"]').value;
    
    if (!clienteId) {
        Swal.fire('Atención', 'Selecciona un cliente', 'warning');
        return false;
    }
    
    if (productos.length === 0) {
        Swal.fire('Atención', 'Agrega al menos un iPhone', 'warning');
        return false;
    }
    
    try {
        // Obtener fotos cargadas
        const fotosCargadas = window.obtenerFotosCargadas_venta ? window.obtenerFotosCargadas_venta() : [];
        
        // Crear FormData
        const formData = new FormData(this);
        const formAction = this.action || window.location.href;
        
        // Enviar via Fetch
        const response = await fetch(formAction, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }

        const data = await response.json();
        if (data.success) {
            // Si hay fotos, subirlas después de crear la venta
            if (fotosCargadas && fotosCargadas.length > 0) {
                for (let i = 0; i < fotosCargadas.length; i++) {
                    const fotosFormData = new FormData();
                    fotosFormData.append('accion', 'upload_venta');
                    fotosFormData.append('venta_id', data.venta_id);
                    fotosFormData.append('iphone_id', '');
                    fotosFormData.append('archivo', fotosCargadas[i].archivo);
                    fotosFormData.append('descripcion', fotosCargadas[i].descripcion || '');
                    
                    try {
                        await fetch('<?php echo BASE_URL; ?>/controllers/fotos-api.php', {
                            method: 'POST',
                            body: fotosFormData
                        });
                    } catch (fotoError) {
                        console.error('Error al subir foto:', fotoError);
                    }
                }
            }
            
            Swal.fire({
                title: '¡Excelente!',
                text: 'Venta registrada correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = '<?php echo BASE_URL; ?>/views/ventas/detalle.php?id=' + data.venta_id;
            });
        } else {
            Swal.fire('Error', data.message || 'Error desconocido', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al procesar: ' + error.message, 'error');
    }
    
    return false;
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
