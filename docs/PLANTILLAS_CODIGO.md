# 📋 PLANTILLAS DE CÓDIGO - GOAPPLE POS

Plantillas reutilizables para crear las vistas CRUD restantes del sistema.

---

## 📝 PLANTILLA: NUEVA VENTA (COMPLEJO)

```php
<?php
/**
 * Nueva Venta - Contado o Crédito
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

// Obtener iPhones disponibles
$iphones_disponibles = $iphoneModel->obtenerDisponibles();

// Procesar venta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos_venta = [
        'cliente_id' => $_POST['cliente_id'],
        'vendedor_id' => usuarioActual()['id'],
        'tipo_venta' => $_POST['tipo_venta'],
        'subtotal' => $_POST['subtotal'],
        'descuento' => $_POST['descuento'] ?? 0,
        'total' => $_POST['total'],
        'forma_pago' => $_POST['forma_pago'],
        'observaciones' => $_POST['observaciones'] ?? ''
    ];

    $productos = json_decode($_POST['productos'], true);

    $resultado = $ventaModel->crear($datos_venta, $productos);

    if ($resultado['success']) {
        // Si es crédito, crear el crédito
        if ($_POST['tipo_venta'] == 'credito') {
            $datos_credito = [
                'venta_id' => $resultado['venta_id'],
                'cliente_id' => $_POST['cliente_id'],
                'monto_total' => $_POST['total'],
                'cuota_inicial' => $_POST['cuota_inicial'],
                'tasa_interes' => $_POST['tasa_interes'],
                'numero_cuotas' => $_POST['numero_cuotas'],
                'fecha_inicio' => date('Y-m-d'),
                'forma_pago_inicial' => $_POST['forma_pago'],
                'usuario_id' => usuarioActual()['id']
            ];

            $creditoModel->crear($datos_credito);
        }

        setFlashMessage('success', 'Venta registrada exitosamente');
        redirect('/views/ventas/detalle.php?id=' . $resultado['venta_id']);
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1><i class="bi bi-cart-plus"></i> Nueva Venta</h1>

    <form method="POST" id="formVenta">
        <div class="row">
            <!-- Selección de Cliente -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-person"></i> Cliente</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select select2" name="cliente_id" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php
                            $clientes = $clienteModel->obtenerTodos();
                            foreach ($clientes as $cliente):
                            ?>
                            <option value="<?php echo $cliente['id']; ?>">
                                <?php echo htmlspecialchars($cliente['nombre']); ?> - 
                                <?php echo $cliente['cedula']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><i class="bi bi-phone"></i> Productos</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select select2" id="seleccionarProducto">
                            <option value="">Seleccionar iPhone...</option>
                            <?php foreach ($iphones_disponibles as $iphone): ?>
                            <option value="<?php echo $iphone['id']; ?>" 
                                    data-precio="<?php echo $iphone['precio_venta']; ?>"
                                    data-modelo="<?php echo htmlspecialchars($iphone['modelo'] . ' ' . $iphone['capacidad']); ?>"
                                    data-imei="<?php echo $iphone['imei']; ?>">
                                <?php echo htmlspecialchars($iphone['modelo'] . ' ' . $iphone['capacidad'] . ' ' . $iphone['color']); ?> - 
                                <?php echo formatearMoneda($iphone['precio_venta']); ?>
                                (IMEI: <?php echo $iphone['imei']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <div id="productosSeleccionados" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tipo de Venta -->
        <div class="card mb-3">
            <div class="card-header">
                <h5><i class="bi bi-credit-card"></i> Tipo de Venta</h5>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="tipo_venta" id="contado" value="contado" checked>
                    <label class="btn btn-outline-success" for="contado">Contado</label>

                    <input type="radio" class="btn-check" name="tipo_venta" id="credito" value="credito">
                    <label class="btn btn-outline-warning" for="credito">Crédito</label>
                </div>

                <!-- Opciones de Crédito (ocultas por defecto) -->
                <div id="opcionesCredito" class="mt-3" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Cuota Inicial</label>
                            <input type="number" class="form-control" name="cuota_inicial" id="cuota_inicial" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tasa Interés (%)</label>
                            <input type="number" class="form-control" name="tasa_interes" value="<?php echo TASA_INTERES_DEFAULT; ?>" step="0.1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">N° Cuotas</label>
                            <input type="number" class="form-control" name="numero_cuotas" value="12">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary mt-4" id="btnCalcularCredito">
                                <i class="bi bi-calculator"></i> Calcular
                            </button>
                        </div>
                    </div>
                    <div id="resultadoCredito" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-receipt"></i> Resumen</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Forma de Pago</label>
                        <select class="form-select" name="forma_pago">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                        
                        <label class="form-label mt-3">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="2"></textarea>
                    </div>
                    <div class="col-md-4">
                        <h4>Subtotal: <span id="subtotal">$ 0</span></h4>
                        <h4>Descuento: <span id="descuento">$ 0</span></h4>
                        <hr>
                        <h3>Total: <span id="total">$ 0</span></h3>

                        <input type="hidden" name="subtotal" id="input_subtotal">
                        <input type="hidden" name="descuento" id="input_descuento" value="0">
                        <input type="hidden" name="total" id="input_total">
                        <input type="hidden" name="productos" id="input_productos">

                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                            <i class="bi bi-check-circle"></i> Completar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let productos = [];

// Agregar producto
$('#seleccionarProducto').on('select2:select', function(e) {
    const option = e.params.data.element;
    const producto = {
        iphone_id: option.value,
        modelo: $(option).data('modelo'),
        imei: $(option).data('imei'),
        precio_unitario: $(option).data('precio'),
        cantidad: 1,
        subtotal: $(option).data('precio')
    };
    
    productos.push(producto);
    actualizarListaProductos();
    actualizarTotales();
    
    // Limpiar selección
    $(this).val(null).trigger('change');
});

function actualizarListaProductos() {
    let html = '<table class="table"><thead><tr><th>Producto</th><th>IMEI</th><th>Precio</th><th>Acción</th></tr></thead><tbody>';
    
    productos.forEach((p, index) => {
        html += `<tr>
            <td>${p.modelo}</td>
            <td>${p.imei}</td>
            <td>${formatearMoneda(p.precio_unitario)}</td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">
                <i class="bi bi-trash"></i></button></td>
        </tr>`;
    });
    
    html += '</tbody></table>';
    $('#productosSeleccionados').html(html);
}

function eliminarProducto(index) {
    productos.splice(index, 1);
    actualizarListaProductos();
    actualizarTotales();
}

function actualizarTotales() {
    const subtotal = productos.reduce((sum, p) => sum + p.subtotal, 0);
    const descuento = 0; // Puedes agregar lógica de descuento
    const total = subtotal - descuento;
    
    $('#subtotal').text(formatearMoneda(subtotal));
    $('#descuento').text(formatearMoneda(descuento));
    $('#total').text(formatearMoneda(total));
    
    $('#input_subtotal').val(subtotal);
    $('#input_descuento').val(descuento);
    $('#input_total').val(total);
    $('#input_productos').val(JSON.stringify(productos));
}

// Cambiar entre contado y crédito
$('input[name="tipo_venta"]').change(function() {
    if ($(this).val() === 'credito') {
        $('#opcionesCredito').slideDown();
    } else {
        $('#opcionesCredito').slideUp();
    }
});

// Calcular crédito
$('#btnCalcularCredito').click(function() {
    const total = parseFloat($('#input_total').val());
    const cuotaInicial = parseFloat($('#cuota_inicial').val());
    const tasaInteres = parseFloat($('input[name="tasa_interes"]').val());
    const numeroCuotas = parseInt($('input[name="numero_cuotas"]').val());
    
    fetch(`../../controllers/api.php?module=creditos&action=calcular&monto_total=${total}&cuota_inicial=${cuotaInicial}&tasa_interes=${tasaInteres}&numero_cuotas=${numeroCuotas}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const c = data.data;
                $('#resultadoCredito').html(`
                    <div class="alert alert-info">
                        <h6>Resultado del Cálculo:</h6>
                        <p><strong>Monto Financiado:</strong> ${formatearMoneda(c.montoFinanciado)}</p>
                        <p><strong>Valor Cuota:</strong> ${formatearMoneda(c.valorCuota)}</p>
                        <p><strong>Total Intereses:</strong> ${formatearMoneda(c.totalIntereses)}</p>
                        <p><strong>Total a Pagar:</strong> ${formatearMoneda(c.totalAPagar)}</p>
                    </div>
                `);
            }
        });
});

// Validar antes de enviar
$('#formVenta').submit(function(e) {
    if (productos.length === 0) {
        e.preventDefault();
        mostrarError('Debe agregar al menos un producto');
        return false;
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
```

---

## 🏢 PLANTILLA: LISTA DE PROVEEDORES

```php
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Proveedor.php';

$page_title = 'Proveedores - ' . APP_NAME;
$proveedorModel = new Proveedor();
$proveedores = $proveedorModel->obtenerTodos($_GET);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h1><i class="bi bi-building"></i> Proveedores</h1>
        <a href="nuevo.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Proveedor
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table id="tabla" class="table table-hover">
                <thead>
                    <tr>
                        <th>NIT/Cédula</th>
                        <th>Nombre</th>
                        <th>Empresa</th>
                        <th>Teléfono</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['nit_cedula']); ?></td>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($p['empresa']); ?></td>
                        <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($p['ciudad']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $p['estado'] == 'activo' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($p['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="ver.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="editar.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$('#tabla').DataTable();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
```

---

## 📱 PLANTILLA: LISTA DE INVENTARIO

```php
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/iPhone.php';

$page_title = 'Inventario - ' . APP_NAME;
$iphoneModel = new iPhone();
$iphones = $iphoneModel->obtenerTodos($_GET);
$stats = $iphoneModel->obtenerEstadisticas();

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1><i class="bi bi-phone"></i> Inventario de iPhones</h1>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <h6>Disponibles</h6>
                    <h3><?php echo $stats['disponibles']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <h6>Vendidos</h6>
                    <h3><?php echo $stats['vendidos']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <h6>En Crédito</h6>
                    <h3><?php echo $stats['en_credito']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <h6>Valor Inventario</h6>
                    <h3><?php echo formatearMoneda($stats['valor_inventario_venta']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between">
            <h5><i class="bi bi-list"></i> Lista de iPhones</h5>
            <a href="nuevo.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Agregar iPhone
            </a>
        </div>
        <div class="card-body">
            <table id="tabla" class="table table-hover">
                <thead>
                    <tr>
                        <th>IMEI</th>
                        <th>Modelo</th>
                        <th>Capacidad</th>
                        <th>Color</th>
                        <th>Condición</th>
                        <th>Batería</th>
                        <th>Proveedor</th>
                        <th>Precio Venta</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($iphones as $iphone): ?>
                    <tr>
                        <td><code><?php echo $iphone['imei']; ?></code></td>
                        <td><strong><?php echo htmlspecialchars($iphone['modelo']); ?></strong></td>
                        <td><?php echo $iphone['capacidad']; ?></td>
                        <td><?php echo htmlspecialchars($iphone['color']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $iphone['condicion'] == 'nuevo' ? 'success' : 'info'; ?>">
                                <?php echo ucfirst($iphone['condicion']); ?>
                            </span>
                        </td>
                        <td><?php echo $iphone['estado_bateria']; ?>%</td>
                        <td><?php echo htmlspecialchars($iphone['proveedor_nombre'] ?? 'N/A'); ?></td>
                        <td><strong><?php echo formatearMoneda($iphone['precio_venta']); ?></strong></td>
                        <td>
                            <?php
                            $badge = match($iphone['estado']) {
                                'disponible' => 'bg-success',
                                'vendido' => 'bg-primary',
                                'en_credito' => 'bg-warning',
                                'apartado' => 'bg-info',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $badge; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $iphone['estado'])); ?>
                            </span>
                        </td>
                        <td>
                            <a href="ver.php?id=<?php echo $iphone['id']; ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if ($iphone['estado'] == 'disponible'): ?>
                            <a href="editar.php?id=<?php echo $iphone['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
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
$('#tabla').DataTable({
    order: [[8, 'asc']]
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
```

---

## 💳 PLANTILLA: REGISTRAR PAGO DE CRÉDITO

```php
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

$page_title = 'Registrar Pago - ' . APP_NAME;
$creditoModel = new Credito();

// Procesar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'credito_id' => $_POST['credito_id'],
        'monto_pago' => $_POST['monto_pago'],
        'numero_cuota' => $_POST['numero_cuota'],
        'forma_pago' => $_POST['forma_pago'],
        'usuario_id' => usuarioActual()['id'],
        'observaciones' => $_POST['observaciones'] ?? ''
    ];

    $resultado = $creditoModel->registrarPago($datos);

    if ($resultado['success']) {
        setFlashMessage('success', 'Pago registrado exitosamente');
        redirect('/views/creditos/ver.php?id=' . $_POST['credito_id']);
    } else {
        setFlashMessage('error', $resultado['message']);
    }
}

// Obtener créditos activos
$creditos_activos = $creditoModel->obtenerTodos(['estado' => 'activo']);

include __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h1><i class="bi bi-cash-coin"></i> Registrar Pago</h1>

            <div class="card shadow mt-4">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Crédito</label>
                            <select class="form-select select2" name="credito_id" id="credito_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($creditos_activos as $credito): ?>
                                <option value="<?php echo $credito['id']; ?>"
                                        data-saldo="<?php echo $credito['saldo_pendiente']; ?>"
                                        data-cuota="<?php echo $credito['valor_cuota']; ?>">
                                    <?php echo $credito['numero_credito']; ?> - 
                                    <?php echo htmlspecialchars($credito['cliente_nombre']); ?> - 
                                    Saldo: <?php echo formatearMoneda($credito['saldo_pendiente']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="infoCredito" class="alert alert-info" style="display: none;"></div>

                        <div class="mb-3">
                            <label class="form-label">Monto del Pago</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="monto_pago" id="monto_pago" required min="1000" step="1000">
                            </div>
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Registrar Pago
                            </button>
                            <a href="lista.php" class="btn btn-secondary">Cancelar</a>
                        </div>
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
        $('#infoCredito').html(`
            <strong>Información del Crédito:</strong><br>
            Saldo Pendiente: ${formatearMoneda(saldo)}<br>
            Valor Cuota: ${formatearMoneda(cuota)}
        `).show();
        
        $('#monto_pago').val(cuota);
    } else {
        $('#infoCredito').hide();
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
```

---

## 📊 USO DE LAS PLANTILLAS

1. **Copiar la plantilla correspondiente**
2. **Ajustar nombres de modelo y variables**
3. **Personalizar campos según necesites**
4. **Guardar en la carpeta correcta**
5. **Probar funcionalidad**

Todas estas plantillas están listas para usar con el sistema base ya creado!
