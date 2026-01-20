<?php
/**
 * Detalle de Venta - Con Edición de Fotos
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Foto.php';

$page_title = 'Detalle de Venta - ' . APP_NAME;

$model = new Venta();
$fotoModel = new Foto();
$id = intval($_GET['id'] ?? 0);
$venta = $model->obtenerPorId($id);

if (!$venta) {
    setFlashMessage('error', 'Venta no encontrada');
    redirect('/views/ventas/lista.php');
}

$detalle = $model->obtenerDetalle($id);
$fotos = $fotoModel->obtenerFotosVenta($id);
$modoEdicion = isset($_GET['editar']) && $_GET['editar'] === '1';
$errores = [];
$exito = false;

// Procesar actualizaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    $observaciones = sanitizar($_POST['observaciones'] ?? '');
    
    // Actualizar observaciones
    if ($model->actualizar($id, ['observaciones' => $observaciones])) {
        // Procesar nuevas fotos si existen
        if (!empty($_FILES['fotos']['tmp_name'][0])) {
            for ($i = 0; $i < count($_FILES['fotos']['tmp_name']); $i++) {
                if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                    $archivo = [
                        'tmp_name' => $_FILES['fotos']['tmp_name'][$i],
                        'name' => $_FILES['fotos']['name'][$i],
                        'type' => $_FILES['fotos']['type'][$i],
                        'error' => $_FILES['fotos']['error'][$i],
                        'size' => $_FILES['fotos']['size'][$i]
                    ];
                    
                    $resultado_foto = Foto::procesarFoto($archivo, 'ventas');
                    if ($resultado_foto['success']) {
                        $descripcion = isset($_POST['descripciones'][$i]) ? sanitizar($_POST['descripciones'][$i]) : '';
                        $iphone_id = $detalle[0]['iphone_id'] ?? null;
                        $fotoModel->cargarFotoVenta($id, $iphone_id, $resultado_foto['archivo'], $descripcion, usuarioActual()['id'] ?? null);
                    }
                }
            }
        }
        
        $exito = true;
        $venta['observaciones'] = $observaciones;
        $fotos = $fotoModel->obtenerFotosVenta($id);
        
        if ($esAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Cambios guardados correctamente']);
            exit;
        }
        
        setFlashMessage('success', 'Venta actualizada correctamente');
        redirect('/views/ventas/detalle.php?id=' . $id);
    } else {
        $errores[] = 'Error al actualizar la venta';
    }
    
    if ($esAjax && !empty($errores)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => implode(' | ', $errores)]);
        exit;
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-receipt"></i> Detalle de Venta</h1>
        <div>
            <?php if (!$modoEdicion): ?>
                <a href="?id=<?php echo $id; ?>&editar=1" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar Fotos
                </a>
            <?php else: ?>
                <a href="?id=<?php echo $id; ?>" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Cancelar
                </a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/views/ventas/lista.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <?php if ($exito): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> Cambios guardados correctamente
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Error:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información General</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Número:</strong> <?php echo htmlspecialchars($venta['numero_venta']); ?></div>
                <div class="col-md-4"><strong>Fecha:</strong> <?php echo formatearFechaHora($venta['fecha_venta']); ?></div>
                <div class="col-md-4"><strong>Cliente:</strong> <?php echo htmlspecialchars($venta['cliente_nombre']); ?></div>
                <div class="col-md-4"><strong>Vendedor:</strong> <?php echo htmlspecialchars($venta['vendedor_nombre']); ?></div>
                <div class="col-md-4"><strong>Tipo:</strong> <span class="badge" style="background: <?php echo $venta['tipo_venta'] === 'contado' ? '#34c759' : '#ff9500'; ?>;"><?php echo ucfirst($venta['tipo_venta']); ?></span></div>
                <div class="col-md-4"><strong>Estado:</strong> <span class="badge bg-info"><?php echo ucfirst($venta['estado']); ?></span></div>
                <div class="col-md-4"><strong>Subtotal:</strong> <?php echo formatearMoneda($venta['subtotal']); ?></div>
                <div class="col-md-4"><strong>Descuento:</strong> <?php echo formatearMoneda($venta['descuento']); ?></div>
                <div class="col-md-4"><strong>Total:</strong> <strong style="color: #0071e3; font-size: 1.1rem;"><?php echo formatearMoneda($venta['total']); ?></strong></div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-phone"></i> Productos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead style="background: #f3f4f6;">
                        <tr>
                            <th>Modelo</th>
                            <th>Capacidad</th>
                            <th>Color</th>
                            <th>IMEI</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $d): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($d['modelo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($d['capacidad']); ?></td>
                                <td><?php echo htmlspecialchars($d['color']); ?></td>
                                <td><code><?php echo htmlspecialchars($d['imei']); ?></code></td>
                                <td><strong style="color: #0071e3;">$<?php echo number_format($d['precio_unitario'], 0, '.', ','); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Observaciones - Editable -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Observaciones</h5>
        </div>
        <div class="card-body">
            <?php if ($modoEdicion): ?>
                <form id="formObservaciones" action="<?php echo htmlspecialchars(BASE_URL . '/views/ventas/detalle.php?id=' . $id); ?>" method="POST">
                    <textarea class="form-control" name="observaciones" rows="3" placeholder="Notas adicionales de la venta..."><?php echo htmlspecialchars($venta['observaciones']); ?></textarea>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Guardar Observaciones
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <p><?php echo nl2br(htmlspecialchars($venta['observaciones'] ?? 'Sin observaciones')); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Fotos de la Venta - Con Edición -->
    <div class="card shadow">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-images"></i> Fotos de la Venta (<?php echo count($fotos); ?>)</h5>
            <?php if ($modoEdicion): ?>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalSubirFotos">
                    <i class="bi bi-plus-circle"></i> Agregar Fotos
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (!empty($fotos)): ?>
                <div class="row g-3">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 position-relative" style="border: 2px solid #e5e7eb;">
                                <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($foto['archivo']); ?>" 
                                     alt="Foto de venta" 
                                     class="card-img-top" 
                                     style="height: 200px; object-fit: cover; cursor: pointer; background: #f0f0f0;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modalFoto<?php echo $foto['id']; ?>"
                                     onerror="this.src='<?php echo ASSETS_URL; ?>/img/placeholder.php'; this.style.opacity='0.5';"
                                     loading="lazy">
                                <div class="card-body">
                                    <p class="card-text small"><?php echo nl2br(htmlspecialchars($foto['descripcion'])); ?></p>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> <?php echo formatearFechaHora($foto['fecha_carga']); ?>
                                    </small>
                                </div>
                                <?php if ($modoEdicion): ?>
                                    <div class="card-footer bg-white">
                                        <button type="button" class="btn btn-sm btn-danger w-100" 
                                                onclick="confirmarEliminarFoto(<?php echo $foto['id']; ?>, 'fotos_venta')">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Modal para ver foto en grande -->
                        <div class="modal fade" id="modalFoto<?php echo $foto['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Foto de Venta</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($foto['archivo']); ?>" 
                                             alt="Foto de venta amplificada"
                                             class="img-fluid" 
                                             style="max-width: 100%; background: #f0f0f0;"
                                             onerror="this.src='<?php echo ASSETS_URL; ?>/img/placeholder.php'; this.style.opacity='0.5';">
                                        <p class="mt-3 text-muted"><?php echo nl2br(htmlspecialchars($foto['descripcion'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem; color: #6b7280;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-image"></i></div>
                    <p>Sin fotos registradas</p>
                    <?php if ($modoEdicion): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSubirFotos">
                            <i class="bi bi-plus-circle"></i> Agregar Primera Foto
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para subir nuevas fotos -->
<?php if ($modoEdicion): ?>
<div class="modal fade" id="modalSubirFotos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cloud-upload"></i> Agregar Fotos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSubirFotos">
                    <?php 
                    $id_zona = 'venta_' . $id;
                    $tipo_fotos = 'de la Venta';
                    include __DIR__ . '/../components/fotos-upload.php'; 
                    ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="enviarFotos()">
                    <i class="bi bi-upload"></i> Subir Fotos
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Guardar observaciones
document.getElementById('formObservaciones')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const formAction = this.action || window.location.href;
    
    try {
        const response = await fetch(formAction, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        
        const data = await response.json();
        if (data.success) {
            Swal.fire({
                title: '¡Excelente!',
                text: 'Observaciones guardadas',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = '<?php echo BASE_URL; ?>/views/ventas/detalle.php?id=<?php echo $id; ?>';
            });
        } else {
            Swal.fire('Error', data.message || 'Error desconocido', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al guardar: ' + error.message, 'error');
    }
});

// Enviar fotos
async function enviarFotos() {
    const idZona = 'venta_<?php echo $id; ?>';
    const fotosCargadas = window['obtenerFotosCargadas' + idZona] ? window['obtenerFotosCargadas' + idZona]() : [];
    
    if (fotosCargadas.length === 0) {
        Swal.fire('Atención', 'Selecciona al menos una foto', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('observaciones', '<?php echo addslashes($venta['observaciones']); ?>');
    formData.append('venta_id', '<?php echo $id; ?>');
    
    for (let i = 0; i < fotosCargadas.length; i++) {
        formData.append('fotos[]', fotosCargadas[i].archivo);
        formData.append('descripciones[]', fotosCargadas[i].descripcion || '');
    }
    
    try {
        const response = await fetch(window.location.href, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        
        const data = await response.json();
        if (data.success) {
            Swal.fire({
                title: '¡Excelente!',
                text: 'Fotos agregadas correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al cargar fotos', 'error');
    }
}

// Eliminar foto
async function confirmarEliminarFoto(fotoId, tabla) {
    const result = await Swal.fire({
        title: '¿Eliminar foto?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if (result.isConfirmed) {
        const formData = new FormData();
        formData.append('accion', 'eliminar');
        formData.append('id', fotoId);
        formData.append('tabla', tabla);
        
        try {
            const response = await fetch('<?php echo BASE_URL; ?>/controllers/fotos-api.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                Swal.fire('Eliminada', 'Foto eliminada correctamente', 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error al eliminar foto', 'error');
        }
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

