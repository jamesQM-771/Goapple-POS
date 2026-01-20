<?php
/**
 * Ver iPhone - Con Edición de Fotos
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/iPhone.php';
require_once __DIR__ . '/../../models/Foto.php';

$page_title = 'Detalle iPhone - ' . APP_NAME;

$model = new iPhone();
$fotoModel = new Foto();
$id = intval($_GET['id'] ?? 0);
$iphone = $model->obtenerPorId($id);

if (!$iphone) {
    setFlashMessage('error', 'iPhone no encontrado');
    redirect('/views/inventario/lista.php');
}

$fotos = $fotoModel->obtenerFotosCompra($id);
$modoEdicion = isset($_GET['editar']) && $_GET['editar'] === '1';
$errores = [];

// Procesar actualizaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    // Actualizar observaciones
    $observaciones = sanitizar($_POST['observaciones'] ?? '');
    if ($model->actualizar($id, ['observaciones' => $observaciones])) {
        $iphone['observaciones'] = $observaciones;
        $fotos = $fotoModel->obtenerFotosCompra($id);
        
        if ($esAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Cambios guardados correctamente']);
            exit;
        }
        setFlashMessage('success', 'iPhone actualizado correctamente');
        redirect('/views/inventario/ver.php?id=' . $id);
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
        <h1><i class="bi bi-phone"></i> Detalle iPhone</h1>
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
            <a href="<?php echo BASE_URL; ?>/views/inventario/lista.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Modelo:</strong> <?php echo htmlspecialchars($iphone['modelo']); ?></div>
                <div class="col-md-4"><strong>Capacidad:</strong> <?php echo htmlspecialchars($iphone['capacidad']); ?></div>
                <div class="col-md-4"><strong>Color:</strong> <?php echo htmlspecialchars($iphone['color']); ?></div>
                <div class="col-md-4"><strong>Condición:</strong> <?php echo ucfirst($iphone['condicion']); ?></div>
                <div class="col-md-4"><strong>Batería:</strong> <?php echo intval($iphone['estado_bateria']); ?>%</div>
                <div class="col-md-4"><strong>IMEI:</strong> <code><?php echo htmlspecialchars($iphone['imei']); ?></code></div>
                <div class="col-md-4"><strong>Proveedor:</strong> <?php echo htmlspecialchars($iphone['proveedor_nombre'] ?? ''); ?></div>
                <div class="col-md-4"><strong>Precio compra:</strong> <strong class="text-accent">$<?php echo number_format($iphone['precio_compra'], 0, '.', ','); ?></strong></div>
                <div class="col-md-4"><strong>Precio venta:</strong> <strong class="text-success">$<?php echo number_format($iphone['precio_venta'], 0, '.', ','); ?></strong></div>
                <div class="col-md-4"><strong>Estado:</strong> <span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $iphone['estado'])); ?></span></div>
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
                <form id="formObservaciones" action="<?php echo htmlspecialchars(BASE_URL . '/views/inventario/ver.php?id=' . $id); ?>" method="POST">
                    <textarea class="form-control" name="observaciones" rows="3" placeholder="Notas adicionales del iPhone..."><?php echo htmlspecialchars($iphone['observaciones'] ?? ''); ?></textarea>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Guardar Observaciones
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <p><?php echo nl2br(htmlspecialchars($iphone['observaciones'] ?? 'Sin observaciones')); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Fotos de la Compra - Con Edición -->
    <div class="card shadow">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-images"></i> Fotos de la Compra (<?php echo count($fotos); ?>)</h5>
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
                                <div class="card h-100 position-relative card-outline">
                                <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($foto['archivo']); ?>" 
                                     alt="Foto de compra" 
                                     class="card-img-top" 
                                         class="card-img-top img-cover" 
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
                                                onclick="confirmarEliminarFoto(<?php echo $foto['id']; ?>, 'fotos_compra')">
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
                                        <h5 class="modal-title">Foto de Compra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="<?php echo UPLOADS_URL; ?>/<?php echo htmlspecialchars($foto['archivo']); ?>" 
                                            alt="Foto de compra amplificada"
                                            class="img-fluid modal-img" 
                                            onerror="this.src='<?php echo ASSETS_URL; ?>/img/placeholder.php'; this.style.opacity='0.5';">
                                        <p class="mt-3 text-muted"><?php echo nl2br(htmlspecialchars($foto['descripcion'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon-large"><i class="bi bi-image"></i></div>
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
                    $id_zona = 'compra_' . $id;
                    $tipo_fotos = 'de la Compra';
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
                window.location.reload();
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
    const idZona = 'compra_<?php echo $id; ?>';
    const fotosCargadas = window['obtenerFotosCargadas' + idZona] ? window['obtenerFotosCargadas' + idZona]() : [];
    
    if (fotosCargadas.length === 0) {
        Swal.fire('Atención', 'Selecciona al menos una foto', 'warning');
        return;
    }
    
    for (let i = 0; i < fotosCargadas.length; i++) {
        const fotosFormData = new FormData();
        fotosFormData.append('accion', 'upload_compra');
        fotosFormData.append('iphone_id', '<?php echo $id; ?>');
        fotosFormData.append('archivo', fotosCargadas[i].archivo);
        fotosFormData.append('descripcion', fotosCargadas[i].descripcion || '');
        
        try {
            await fetch('<?php echo BASE_URL; ?>/controllers/fotos-api.php', {
                method: 'POST',
                body: fotosFormData
            });
        } catch (error) {
            console.error('Error al subir foto:', error);
        }
    }
    
    Swal.fire({
        title: '¡Excelente!',
        text: 'Fotos agregadas correctamente',
        icon: 'success',
        confirmButtonText: 'Aceptar'
    }).then(() => {
        window.location.reload();
    });
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
