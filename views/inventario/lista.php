<?php
/**
 * Lista de Inventario
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/iPhone.php';
require_once __DIR__ . '/../../models/Proveedor.php';

$page_title = 'Inventario - ' . APP_NAME;

$iphoneModel = new iPhone();
$proveedorModel = new Proveedor();

$iphones = $iphoneModel->obtenerTodos($_GET);
$stats = $iphoneModel->obtenerEstadisticas();
$proveedores = $proveedorModel->obtenerParaSelect();

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h1><i class="bi bi-phone"></i> Inventario</h1>
        <a href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Agregar iPhone
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-start border-success border-4 shadow-sm"><div class="card-body"><h6>Disponibles</h6><h3><?php echo $stats['disponibles']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-primary border-4 shadow-sm"><div class="card-body"><h6>Vendidos</h6><h3><?php echo $stats['vendidos']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-warning border-4 shadow-sm"><div class="card-body"><h6>En crédito</h6><h3><?php echo $stats['en_credito']; ?></h3></div></div></div>
        <div class="col-md-3"><div class="card border-start border-info border-4 shadow-sm"><div class="card-body"><h6>Valor inventario</h6><h3><?php echo formatearMoneda($stats['valor_inventario_venta']); ?></h3></div></div></div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="buscar" placeholder="Buscar modelo, IMEI, color" value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="estado">
                        <option value="">Estado</option>
                        <option value="disponible" <?php echo ($_GET['estado'] ?? '') === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                        <option value="vendido" <?php echo ($_GET['estado'] ?? '') === 'vendido' ? 'selected' : ''; ?>>Vendido</option>
                        <option value="en_credito" <?php echo ($_GET['estado'] ?? '') === 'en_credito' ? 'selected' : ''; ?>>En crédito</option>
                        <option value="apartado" <?php echo ($_GET['estado'] ?? '') === 'apartado' ? 'selected' : ''; ?>>Apartado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="condicion">
                        <option value="">Condición</option>
                        <option value="nuevo" <?php echo ($_GET['condicion'] ?? '') === 'nuevo' ? 'selected' : ''; ?>>Nuevo</option>
                        <option value="usado" <?php echo ($_GET['condicion'] ?? '') === 'usado' ? 'selected' : ''; ?>>Usado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="proveedor_id">
                        <option value="">Proveedor</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo (string)($_GET['proveedor_id'] ?? '') === (string)$p['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
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
                            <td><?php echo htmlspecialchars($iphone['imei']); ?></td>
                            <td><?php echo htmlspecialchars($iphone['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($iphone['capacidad']); ?></td>
                            <td><?php echo htmlspecialchars($iphone['color']); ?></td>
                            <td><?php echo ucfirst($iphone['condicion']); ?></td>
                            <td><?php echo intval($iphone['estado_bateria']); ?>%</td>
                            <td><?php echo htmlspecialchars($iphone['proveedor_nombre'] ?? ''); ?></td>
                            <td><?php echo formatearMoneda($iphone['precio_venta']); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $iphone['estado'])); ?></td>
                            <td>
                                <a class="btn btn-sm btn-info" href="<?php echo BASE_URL; ?>/views/inventario/ver.php?id=<?php echo $iphone['id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-warning" href="<?php echo BASE_URL; ?>/views/inventario/editar.php?id=<?php echo $iphone['id']; ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="eliminarIphone(<?php echo $iphone['id']; ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function eliminarIphone(id) {
    confirmarEliminacion('¿Eliminar este iPhone?').then((result) => {
        if (result.isConfirmed) {
            fetch(`<?php echo BASE_URL; ?>/controllers/api.php?module=iphones&action=delete&id=${id}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        mostrarExito(data.message || 'iPhone eliminado');
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
