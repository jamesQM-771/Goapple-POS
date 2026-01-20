<?php
/**
 * Editar iPhone
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/iPhone.php';
require_once __DIR__ . '/../../models/Proveedor.php';
require_once __DIR__ . '/../../models/Foto.php';

$page_title = 'Editar iPhone - ' . APP_NAME;

$iphoneModel = new iPhone();
$proveedorModel = new Proveedor();
$fotoModel = new Foto();
$proveedores = $proveedorModel->obtenerParaSelect();

$id = intval($_GET['id'] ?? 0);
$iphone = $iphoneModel->obtenerPorId($id);

if (!$iphone) {
    setFlashMessage('error', 'iPhone no encontrado');
    redirect('/views/inventario/lista.php');
}

$fotos = $fotoModel->obtenerFotosCompra($id);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'modelo' => sanitizar($_POST['modelo'] ?? ''),
        'capacidad' => sanitizar($_POST['capacidad'] ?? ''),
        'color' => sanitizar($_POST['color'] ?? ''),
        'condicion' => sanitizar($_POST['condicion'] ?? 'nuevo'),
        'estado_bateria' => intval($_POST['estado_bateria'] ?? 100),
        'imei' => sanitizar($_POST['imei'] ?? ''),
        'proveedor_id' => sanitizar($_POST['proveedor_id'] ?? null),
        'precio_compra' => floatval($_POST['precio_compra'] ?? 0),
        'precio_venta' => floatval($_POST['precio_venta'] ?? 0),
        'estado' => sanitizar($_POST['estado'] ?? 'disponible'),
        'observaciones' => sanitizar($_POST['observaciones'] ?? '')
    ];

    if (empty($datos['modelo'])) $errores[] = 'El modelo es obligatorio';
    if (empty($datos['capacidad'])) $errores[] = 'La capacidad es obligatoria';
    if (empty($datos['color'])) $errores[] = 'El color es obligatorio';
    if (empty($datos['imei'])) $errores[] = 'El IMEI es obligatorio';
    if (!preg_match('/^\d{15}$/', $datos['imei'])) $errores[] = 'El IMEI debe tener 15 dígitos';

    if ($iphoneModel->imeiExiste($datos['imei'], $id)) $errores[] = 'El IMEI ya está registrado';

    if (empty($errores)) {
        if ($iphoneModel->actualizar($id, $datos)) {
            setFlashMessage('success', 'iPhone actualizado correctamente');
            redirect('/views/inventario/ver.php?id=' . $id);
        } else {
            $errores[] = 'No se pudo actualizar el iPhone';
        }
    }

    $iphone = array_merge($iphone, $datos);
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil"></i> Editar iPhone</h1>
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
            <form method="POST" action="<?php echo htmlspecialchars(BASE_URL . '/views/inventario/editar.php?id=' . $id); ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Modelo</label>
                    <input type="text" class="form-control" name="modelo" value="<?php echo htmlspecialchars($iphone['modelo']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Capacidad</label>
                    <input type="text" class="form-control" name="capacidad" value="<?php echo htmlspecialchars($iphone['capacidad']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color" value="<?php echo htmlspecialchars($iphone['color']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Condición</label>
                    <select class="form-select" name="condicion">
                        <option value="nuevo" <?php echo $iphone['condicion'] === 'nuevo' ? 'selected' : ''; ?>>Nuevo</option>
                        <option value="usado" <?php echo $iphone['condicion'] === 'usado' ? 'selected' : ''; ?>>Usado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batería (%)</label>
                    <input type="number" class="form-control" name="estado_bateria" value="<?php echo htmlspecialchars($iphone['estado_bateria']); ?>" min="0" max="100">
                </div>
                <div class="col-md-3">
                    <label class="form-label">IMEI</label>
                    <input type="text" class="form-control" name="imei" value="<?php echo htmlspecialchars($iphone['imei']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Proveedor</label>
                    <select class="form-select" name="proveedor_id">
                        <option value="">Seleccionar</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo (string)$iphone['proveedor_id'] === (string)$p['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio Compra</label>
                    <input type="number" class="form-control" name="precio_compra" value="<?php echo htmlspecialchars($iphone['precio_compra']); ?>" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio Venta</label>
                    <input type="number" class="form-control" name="precio_venta" value="<?php echo htmlspecialchars($iphone['precio_venta']); ?>" min="0" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="disponible" <?php echo $iphone['estado'] === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                        <option value="vendido" <?php echo $iphone['estado'] === 'vendido' ? 'selected' : ''; ?>>Vendido</option>
                        <option value="en_credito" <?php echo $iphone['estado'] === 'en_credito' ? 'selected' : ''; ?>>En crédito</option>
                        <option value="apartado" <?php echo $iphone['estado'] === 'apartado' ? 'selected' : ''; ?>>Apartado</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" name="observaciones" rows="3"><?php echo htmlspecialchars($iphone['observaciones']); ?></textarea>
                </div>
                
                <!-- Fotos Existentes -->
                <?php if (!empty($fotos)): ?>
                <div class="col-12 mt-3">
                    <h6 class="mb-3"><i class="bi bi-images"></i> Fotos Actuales</h6>
                    <?php 
                    $tabla = 'fotos_compra';
                    $mostrar_eliminar = true;
                    include __DIR__ . '/../components/fotos-galeria.php'; 
                    ?>
                </div>
                <?php endif; ?>
                
                <!-- Agregar Nuevas Fotos -->
                <div class="col-12 mt-3">
                    <?php 
                    $id_zona = 'compra_editar';
                    $tipo_fotos = 'de la Compra';
                    include __DIR__ . '/../components/fotos-upload.php'; 
                    ?>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/inventario/ver.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const idZona = 'compra_editar';
    const fotosCargadas = window['obtenerFotosCargadas' + idZona] ? window['obtenerFotosCargadas' + idZona]() : [];
    
    // Enviar el formulario normalmente primero
    const formData = new FormData(this);
    const formAction = this.action || window.location.href;
    
    try {
        const response = await fetch(formAction, {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        
        // Después de guardar el iPhone, si hay fotos, subirlas
        if (fotosCargadas && fotosCargadas.length > 0) {
            for (let i = 0; i < fotosCargadas.length; i++) {
                const fotosFormData = new FormData();
                fotosFormData.append('accion', 'upload_compra');
                fotosFormData.append('iphone_id', '<?php echo $id; ?>');
                fotosFormData.append('archivo', fotosCargadas[i].archivo);
                fotosFormData.append('descripcion', fotosCargadas[i].descripcion || '');
                
                try {
                    const fotoResponse = await fetch('<?php echo BASE_URL; ?>/controllers/fotos-api.php', {
                        method: 'POST',
                        body: fotosFormData
                    });
                    
                    if (!fotoResponse.ok) {
                        console.error('Error al subir foto:', fotoResponse.status);
                    }
                } catch (fotoError) {
                    console.error('Error en petición de foto:', fotoError);
                }
            }
        }
        
        // Mostrar confirmación y redirigir
        Swal.fire({
            title: '¡Éxito!',
            text: 'iPhone actualizado correctamente' + (fotosCargadas.length > 0 ? ' con fotos' : ''),
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = '<?php echo BASE_URL; ?>/views/inventario/ver.php?id=<?php echo $id; ?>';
        });
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudo guardar: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
