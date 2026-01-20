<?php
/**
 * Crear Nuevo Cliente
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Cliente.php';

$page_title = 'Nuevo Cliente - ' . APP_NAME;
$clienteModel = new Cliente();
$errores = [];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => sanitizar($_POST['nombre'] ?? ''),
        'cedula' => sanitizar($_POST['cedula'] ?? ''),
        'telefono' => sanitizar($_POST['telefono'] ?? ''),
        'email' => sanitizar($_POST['email'] ?? ''),
        'direccion' => sanitizar($_POST['direccion'] ?? ''),
        'ciudad' => sanitizar($_POST['ciudad'] ?? ''),
        'limite_credito' => floatval($_POST['limite_credito'] ?? 0),
        'credito_disponible' => floatval($_POST['limite_credito'] ?? 0),
        'notas' => sanitizar($_POST['notas'] ?? '')
    ];

    // Validaciones
    if (empty($datos['nombre'])) {
        $errores[] = 'El nombre es obligatorio';
    }
    
    if (empty($datos['cedula'])) {
        $errores[] = 'La cédula es obligatoria';
    } elseif ($clienteModel->cedulaExiste($datos['cedula'])) {
        $errores[] = 'Ya existe un cliente con esta cédula';
    }
    
    if (empty($datos['telefono'])) {
        $errores[] = 'El teléfono es obligatorio';
    }

    // Si no hay errores, crear el cliente
    if (empty($errores)) {
        $id = $clienteModel->crear($datos);
        
        if ($id) {
            setFlashMessage('success', 'Cliente creado exitosamente');
            redirect('/views/clientes/lista.php');
        } else {
            $errores[] = 'Error al crear el cliente';
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-person-plus-fill"></i> Nuevo Cliente</h1>
                <a href="lista.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <!-- Errores -->
            <?php if (!empty($errores)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Errores en el formulario:</h6>
                <ul class="mb-0">
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Datos del Cliente</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <!-- Información Personal -->
                        <h6 class="border-bottom pb-2 mb-3">Información Personal</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" 
                                       required>
                                <div class="invalid-feedback">Por favor ingrese el nombre</div>
                            </div>

                            <div class="col-md-4">
                                <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cedula" name="cedula" 
                                       value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>" 
                                       pattern="[0-9]{7,10}" required>
                                <div class="invalid-feedback">Cédula válida (7-10 dígitos)</div>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Información de Contacto</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" 
                                           placeholder="+57 300 123 4567" required>
                                </div>
                                <div class="invalid-feedback">Por favor ingrese el teléfono</div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                           placeholder="cliente@ejemplo.com">
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" 
                                       value="<?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?>" 
                                       placeholder="Calle 123 #45-67">
                            </div>

                            <div class="col-md-4">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" 
                                       value="<?php echo htmlspecialchars($_POST['ciudad'] ?? 'Bogotá'); ?>">
                            </div>
                        </div>

                        <!-- Configuración de Crédito -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Configuración de Crédito</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="limite_credito" class="form-label">Límite de Crédito</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="limite_credito" name="limite_credito" 
                                           value="<?php echo htmlspecialchars($_POST['limite_credito'] ?? '0'); ?>" 
                                           min="0" step="1000">
                                    <span class="input-group-text">.00</span>
                                </div>
                                <small class="text-muted">Monto máximo que puede tener en créditos</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Crédito Disponible Inicial</label>
                                <input type="text" class="form-control" 
                                       value="Igual al límite de crédito" disabled>
                                <small class="text-muted">Se ajustará automáticamente</small>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-3">
                            <label for="notas" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" id="notas" name="notas" rows="3" 
                                      placeholder="Observaciones, referencias, etc."><?php echo htmlspecialchars($_POST['notas'] ?? ''); ?></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="lista.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de formulario Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Formatear número de teléfono mientras se escribe
document.getElementById('telefono').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) value = value.substr(0, 10);
    e.target.value = value;
});

// Formatear cédula
document.getElementById('cedula').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) value = value.substr(0, 10);
    e.target.value = value;
});

// Formatear límite de crédito
document.getElementById('limite_credito').addEventListener('input', function(e) {
    const value = parseFloat(e.target.value) || 0;
    if (value < 0) e.target.value = 0;
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
