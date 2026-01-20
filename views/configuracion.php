<?php
/**
 * Configuración del sistema (solo admin)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Acceso denegado');
    redirect('/views/dashboard.php');
}

$page_title = 'Configuración - ' . APP_NAME;

$db = Database::getInstance();
$conn = $db->getConnection();

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config = [
        'empresa_nombre' => sanitizar($_POST['empresa_nombre'] ?? ''),
        'empresa_nit' => sanitizar($_POST['empresa_nit'] ?? ''),
        'empresa_telefono' => sanitizar($_POST['empresa_telefono'] ?? ''),
        'empresa_email' => sanitizar($_POST['empresa_email'] ?? ''),
        'empresa_direccion' => sanitizar($_POST['empresa_direccion'] ?? ''),
        'tasa_interes_default' => sanitizar($_POST['tasa_interes_default'] ?? ''),
        'dias_mora_tolerancia' => sanitizar($_POST['dias_mora_tolerancia'] ?? '')
    ];

    if (empty($config['empresa_nombre'])) $errores[] = 'El nombre de la empresa es obligatorio';

    if (empty($errores)) {
        foreach ($config as $clave => $valor) {
            $stmt = $conn->prepare("UPDATE configuracion SET valor = :valor WHERE clave = :clave");
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':clave', $clave);
            $stmt->execute();
        }
        setFlashMessage('success', 'Configuración actualizada');
        redirect('/views/configuracion.php');
    }
}

// Cargar configuración
$stmt = $conn->prepare("SELECT clave, valor FROM configuracion");
$stmt->execute();
$items = $stmt->fetchAll();
$configuracion = [];
foreach ($items as $item) {
    $configuracion[$item['clave']] = $item['valor'];
}

include __DIR__ . '/layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="bi bi-gear"></i> Configuración</h1>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control" name="empresa_nombre" value="<?php echo htmlspecialchars($configuracion['empresa_nombre'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIT</label>
                    <input type="text" class="form-control" name="empresa_nit" value="<?php echo htmlspecialchars($configuracion['empresa_nit'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" name="empresa_telefono" value="<?php echo htmlspecialchars($configuracion['empresa_telefono'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="empresa_email" value="<?php echo htmlspecialchars($configuracion['empresa_email'] ?? ''); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="empresa_direccion" value="<?php echo htmlspecialchars($configuracion['empresa_direccion'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tasa interés (%)</label>
                    <input type="number" class="form-control" name="tasa_interes_default" value="<?php echo htmlspecialchars($configuracion['tasa_interes_default'] ?? ''); ?>" step="0.1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Días mora tolerancia</label>
                    <input type="number" class="form-control" name="dias_mora_tolerancia" value="<?php echo htmlspecialchars($configuracion['dias_mora_tolerancia'] ?? ''); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
