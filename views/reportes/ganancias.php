<?php
/**
 * Reporte de Ganancias
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = 'Reporte de Ganancias - ' . APP_NAME;

$db = Database::getInstance();
$conn = $db->getConnection();

$query = "SELECT 
            SUM(CASE WHEN estado IN ('vendido', 'en_credito') THEN precio_venta ELSE 0 END) AS total_venta,
            SUM(CASE WHEN estado IN ('vendido', 'en_credito') THEN precio_compra ELSE 0 END) AS total_compra,
            SUM(CASE WHEN estado IN ('vendido', 'en_credito') THEN (precio_venta - precio_compra) ELSE 0 END) AS margen
          FROM iphones";

$stmt = $conn->prepare($query);
$stmt->execute();
$datos = $stmt->fetch();

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="bi bi-currency-dollar"></i> Reporte de Ganancias</h1>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <h6>Total ventas</h6>
                    <h3><?php echo formatearMoneda($datos['total_venta'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <h6>Total compras</h6>
                    <h3><?php echo formatearMoneda($datos['total_compra'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <h6>Margen</h6>
                    <h3><?php echo formatearMoneda($datos['margen'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        Este reporte calcula el margen considerando iPhones vendidos o en crédito.
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
