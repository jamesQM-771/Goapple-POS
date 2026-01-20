<?php
/**
 * Eliminar Cliente
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Cliente.php';

if (!esAdmin()) {
    setFlashMessage('error', 'Solo el administrador puede eliminar clientes');
    redirect('/views/clientes/lista.php');
}

$page_title = 'Eliminar Cliente - ' . APP_NAME;

$clienteModel = new Cliente();
$id = intval($_GET['id'] ?? 0);

if (!$id) {
    setFlashMessage('error', 'ID de cliente inválido');
    redirect('/views/clientes/lista.php');
}

$cliente = $clienteModel->obtenerPorId($id);
if (!$cliente) {
    setFlashMessage('error', 'Cliente no encontrado');
    redirect('/views/clientes/lista.php');
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $clienteModel->eliminar($id, true); // forzar = true para admin
    
    if ($result['success']) {
        setFlashMessage('success', $result['message']);
        redirect('/views/clientes/lista.php');
    } else {
        setFlashMessage('error', $result['message']);
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación</h4>
                </div>
                <div class="card-body">
                    <p class="lead">¿Está seguro de eliminar este cliente?</p>
                    
                    <div class="alert alert-warning">
                        <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong><br>
                        <small>Cédula: <?php echo htmlspecialchars($cliente['cedula']); ?></small><br>
                        <small>Teléfono: <?php echo htmlspecialchars($cliente['telefono']); ?></small>
                    </div>

                    <div class="alert alert-danger">
                        <i class="bi bi-warning"></i>
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                    </div>

                    <form method="POST">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                            <a href="<?php echo BASE_URL; ?>/views/clientes/lista.php" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
