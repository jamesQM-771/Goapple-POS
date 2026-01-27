<?php
/**
 * Lista de Clientes
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Cliente.php';

$page_title = 'Clientes - ' . APP_NAME;

$clienteModel = new Cliente();

// Filtros
$filtros = [];
if (!empty($_GET['buscar'])) {
    $filtros['buscar'] = $_GET['buscar'];
}
if (!empty($_GET['estado'])) {
    $filtros['estado'] = $_GET['estado'];
}

$clientes = $clienteModel->obtenerTodos($filtros);
$stats = $clienteModel->obtenerEstadisticasGenerales();

include __DIR__ . '/../layouts/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-0">
                <i class="bi bi-people-fill text-accent"></i> Gestión de Clientes
            </h1>
            <p class="page-description">Administra todos tus clientes y su historial de compras</p>
        </div>
        <a href="nuevo.php" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i> Nuevo Cliente
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Total Clientes</h6>
                        <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                    </div>
                    <i class="bi bi-people icon-lg-muted text-accent-info"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-success">Activos</h6>
                        <div class="stat-value text-success"><?php echo $stats['activos'] ?? 0; ?></div>
                    </div>
                    <i class="bi bi-check-circle icon-lg-muted text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-attention">Morosos</h6>
                        <div class="stat-value stat-value-attention"><?php echo $stats['morosos'] ?? 0; ?></div>
                    </div>
                    <i class="bi bi-exclamation-circle icon-lg-muted text-attention"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-danger">Bloqueados</h6>
                        <div class="stat-value stat-value-danger"><?php echo $stats['bloqueados'] ?? 0; ?></div>
                    </div>
                    <i class="bi bi-lock-fill icon-lg-muted text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header card-header-white">
            <h6 class="mb-0 fw-semibold text-dark">
                <i class="bi bi-funnel"></i> Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Buscar Cliente</label>
                    <input type="text" class="form-control" name="buscar" 
                           placeholder="Nombre, cédula, teléfono o email..." 
                           value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="activo" <?php echo ($_GET['estado'] ?? '') == 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="moroso" <?php echo ($_GET['estado'] ?? '') == 'moroso' ? 'selected' : ''; ?>>Moroso</option>
                        <option value="bloqueado" <?php echo ($_GET['estado'] ?? '') == 'bloqueado' ? 'selected' : ''; ?>>Bloqueado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-flex">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="lista.php" class="btn btn-outline-secondary btn-padded">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Lista de Clientes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaClientes" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th>Estado</th>
                            <th>Total Compras</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($cliente['cedula']); ?></strong></td>
                            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                            <td>
                                <i class="bi bi-phone"></i> 
                                <?php echo htmlspecialchars($cliente['telefono']); ?>
                            </td>
                            <td>
                                <?php if ($cliente['email']): ?>
                                    <i class="bi bi-envelope"></i> 
                                    <?php echo htmlspecialchars($cliente['email']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin email</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($cliente['ciudad'] ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                $badge_class = match($cliente['estado']) {
                                    'activo' => 'bg-success',
                                    'moroso' => 'bg-warning',
                                    'bloqueado' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo ucfirst($cliente['estado']); ?>
                                </span>
                            </td>
                            <td><strong><?php echo formatearMoneda($cliente['total_compras']); ?></strong></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="ver.php?id=<?php echo $cliente['id']; ?>" 
                                       class="btn btn-info" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="editar.php?id=<?php echo $cliente['id']; ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-danger" 
                                            onclick="eliminarCliente(<?php echo $cliente['id']; ?>, '<?php echo htmlspecialchars(addslashes($cliente['nombre'])); ?>')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function eliminarCliente(id, nombre) {
    confirmarEliminacion('¿Eliminar al cliente ' + nombre + '?').then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo BASE_URL; ?>/controllers/api.php?module=clientes&action=delete&id=' + id)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        mostrarExito(data.message || 'Cliente eliminado');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        mostrarError(data.message || 'No se pudo eliminar');
                    }
                });
        }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
