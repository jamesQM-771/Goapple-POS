<?php
/**
 * Configuración de Comisiones (Admin) - Versión Mejorada
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Comision.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Configuración de Comisiones - ' . APP_NAME;

$comisionModel = new Comision();
$usuarioModel = new Usuario();

// Obtener vendedores activos
try {
    $vendedores = $usuarioModel->obtenerTodos(['rol' => 'vendedor', 'estado' => 'activo']);
    if (!is_array($vendedores)) {
        $vendedores = [];
    }
} catch (Exception $e) {
    $vendedores = [];
}

// Procesar POST
$mensaje = '';
$tipo_mensaje = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendedor_id = intval($_POST['vendedor_id'] ?? 0);
    
    if ($vendedor_id > 0) {
        $datos = [
            'porcentaje' => floatval($_POST['porcentaje'] ?? COMISION_DEFAULT_PCT),
            'meta_mensual' => floatval($_POST['meta_mensual'] ?? 0),
            'bono_meta' => floatval($_POST['bono_meta'] ?? 0),
            'descuento_fijo' => floatval($_POST['descuento_fijo'] ?? 0),
            'retencion_pct' => floatval($_POST['retencion_pct'] ?? 0)
        ];

        try {
            $ok = $comisionModel->guardarConfig($vendedor_id, $datos);
            if ($ok) {
                $tipo_mensaje = 'success';
                $mensaje = 'Configuración guardada exitosamente';
            } else {
                $tipo_mensaje = 'danger';
                $mensaje = 'No se pudo guardar la configuración';
            }
        } catch (Exception $e) {
            $tipo_mensaje = 'danger';
            $mensaje = 'Error: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-gear"></i> Configuración de Comisiones por Vendedor</h1>
        <a href="<?php echo BASE_URL; ?>/views/comisiones/reportes.php" class="btn btn-outline-primary">
            <i class="bi bi-graph-up"></i> Ver Reportes
        </a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($vendedores)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No hay vendedores activos en el sistema. 
            <a href="<?php echo BASE_URL; ?>/views/usuarios/nuevo.php" class="alert-link">Crear un vendedor</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($vendedores as $v): ?>
                <?php $cfg = $comisionModel->obtenerConfig($v['id']); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($v['nombre']); ?></h5>
                            <small class="text-muted"><?php echo htmlspecialchars($v['email']); ?></small>
                        </div>
                        <form method="POST" class="comision-form">
                            <div class="card-body">
                                <input type="hidden" name="vendedor_id" value="<?php echo $v['id']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">% Comisión Base</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="porcentaje" 
                                               value="<?php echo htmlspecialchars($cfg['porcentaje']); ?>" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meta Mensual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" name="meta_mensual" 
                                               value="<?php echo htmlspecialchars($cfg['meta_mensual']); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bono por Meta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" name="bono_meta" 
                                               value="<?php echo htmlspecialchars($cfg['bono_meta']); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descuento Fijo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" name="descuento_fijo" 
                                               value="<?php echo htmlspecialchars($cfg['descuento_fijo']); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Retención %</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="retencion_pct" 
                                               value="<?php echo htmlspecialchars($cfg['retencion_pct']); ?>" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle"></i> Guardar Configuración
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
