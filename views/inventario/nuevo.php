<?php
/**
 * Nuevo iPhone
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/iPhone.php';
require_once __DIR__ . '/../../models/Proveedor.php';
require_once __DIR__ . '/../../models/Foto.php';

$page_title = 'Agregar iPhone - ' . APP_NAME;

$iphoneModel = new iPhone();
$proveedorModel = new Proveedor();
$fotoModel = new Foto();
$proveedores = $proveedorModel->obtenerParaSelect();

$errores = [];
$valores = [
    'modelo' => '',
    'capacidad' => '',
    'color' => '',
    'condicion' => 'nuevo',
    'estado_bateria' => 100,
    'imei' => '',
    'proveedor_id' => '',
    'precio_compra' => '',
    'precio_venta' => '',
    'estado' => 'disponible',
    'observaciones' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($valores as $k => $v) {
        $valores[$k] = sanitizar($_POST[$k] ?? $v);
    }

    if (empty($valores['modelo'])) $errores[] = 'El modelo es obligatorio';
    if (empty($valores['capacidad'])) $errores[] = 'La capacidad es obligatoria';
    if (empty($valores['color'])) $errores[] = 'El color es obligatorio';
    if (empty($valores['imei'])) $errores[] = 'El IMEI es obligatorio';
    if (!preg_match('/^\d{15}$/', $valores['imei'])) $errores[] = 'El IMEI debe tener 15 dígitos';
    if (empty($valores['precio_compra'])) $errores[] = 'El precio de compra es obligatorio';
    if (empty($valores['precio_venta'])) $errores[] = 'El precio de venta es obligatorio';

    if ($iphoneModel->imeiExiste($valores['imei'])) $errores[] = 'El IMEI ya está registrado';

    if (empty($errores)) {
        $id = $iphoneModel->crear($valores);
        if ($id) {
            // Procesar fotos si existen
            if (!empty($_FILES['fotos']['name'][0])) {
                foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                    if (!empty($_FILES['fotos']['name'][$key])) {
                        $archivo = $_FILES['fotos']['tmp_name'][$key];
                        $nombre_original = $_FILES['fotos']['name'][$key];
                        $descripcion = sanitizar($_POST['fotos_descripcion'][$key] ?? '');
                        
                        if ($fotoModel->cargarFotoCompra($id, $archivo, $descripcion, $_SESSION['usuario_id'] ?? 0)) {
                            // Foto guardada exitosamente
                        }
                    }
                }
            }
            
            setFlashMessage('success', 'iPhone agregado correctamente' . (!empty($_FILES['fotos']['name'][0]) ? ' con fotos' : ''));
            redirect('/views/inventario/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo agregar el iPhone';
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-plus-circle"></i> Agregar iPhone</h1>
        <a href="<?php echo BASE_URL; ?>/views/inventario/lista.php" class="btn btn-outline-secondary">
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
            <form method="POST" action="<?php echo htmlspecialchars(BASE_URL . '/views/inventario/nuevo.php'); ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Modelo</label>
                    <input type="text" class="form-control" name="modelo" value="<?php echo htmlspecialchars($valores['modelo']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Capacidad</label>
                    <input type="text" class="form-control" name="capacidad" value="<?php echo htmlspecialchars($valores['capacidad']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color" value="<?php echo htmlspecialchars($valores['color']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Condición</label>
                    <select class="form-select" name="condicion">
                        <option value="nuevo" <?php echo $valores['condicion'] === 'nuevo' ? 'selected' : ''; ?>>Nuevo</option>
                        <option value="usado" <?php echo $valores['condicion'] === 'usado' ? 'selected' : ''; ?>>Usado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batería (%)</label>
                    <input type="number" class="form-control" name="estado_bateria" value="<?php echo htmlspecialchars($valores['estado_bateria']); ?>" min="0" max="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label">IMEI</label>
                    <input type="text" class="form-control" name="imei" value="<?php echo htmlspecialchars($valores['imei']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Proveedor</label>
                    <select class="form-select" name="proveedor_id">
                        <option value="">Seleccionar</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo (string)$valores['proveedor_id'] === (string)$p['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio Compra</label>
                    <input type="number" class="form-control" name="precio_compra" value="<?php echo htmlspecialchars($valores['precio_compra']); ?>" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio Venta</label>
                    <input type="number" class="form-control" name="precio_venta" value="<?php echo htmlspecialchars($valores['precio_venta']); ?>" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="disponible" <?php echo $valores['estado'] === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                        <option value="vendido" <?php echo $valores['estado'] === 'vendido' ? 'selected' : ''; ?>>Vendido</option>
                        <option value="en_credito" <?php echo $valores['estado'] === 'en_credito' ? 'selected' : ''; ?>>En crédito</option>
                        <option value="apartado" <?php echo $valores['estado'] === 'apartado' ? 'selected' : ''; ?>>Apartado</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" name="observaciones" rows="3"><?php echo htmlspecialchars($valores['observaciones']); ?></textarea>
                </div>
                
                <!-- Fotos de la Compra -->
                <div class="col-12 mt-3">
                    <?php 
                    $id_zona = 'compra';
                    $tipo_fotos = 'de la Compra';
                    include __DIR__ . '/../components/fotos-upload.php'; 
                    ?>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/inventario/lista.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Obtener fotos cargadas del componente
    const fotosCargadas = window.obtenerFotosCargadas_compra ? window.obtenerFotosCargadas_compra() : [];
    
    // Crear FormData con datos del formulario
    const formData = new FormData(this);
    
    // Agregar fotos al FormData (para que se procesen como $_FILES)
    fotosCargadas.forEach((foto, index) => {
        formData.append('fotos[]', foto.archivo);
        formData.append('fotos_descripcion[]', foto.descripcion || '');
    });
    
    const formAction = this.action || window.location.href;
    
    try {
        const response = await fetch(formAction, {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        
        // El servidor hace redirect automático, así que esperamos
        // Si llegamos aquí, el formulario se procesó correctamente
        
        Swal.fire({
            title: '¡Excelente!',
            text: 'iPhone agregado correctamente' + (fotosCargadas.length > 0 ? ' con fotos' : ''),
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            // El servidor redirige automáticamente, pero fallback aquí
            window.location.href = '<?php echo BASE_URL; ?>/views/inventario/lista.php';
        });
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Error al guardar: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
