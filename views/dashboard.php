<?php
/**
 * Dashboard Principal - GoApple
 * Diseño profesional al estilo Apple
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Credito.php';
require_once __DIR__ . '/../models/iPhone.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Apartado.php';

$page_title = 'Dashboard - ' . APP_NAME;

// Obtener estadísticas
$ventaModel = new Venta();
$creditoModel = new Credito();
$iphoneModel = new iPhone();
$clienteModel = new Cliente();
$apartadoModel = new Apartado();

// Estadísticas del mes actual
$fecha_inicio_mes = date('Y-m-01');
$fecha_fin_mes = date('Y-m-t');

$stats_ventas = $ventaModel->obtenerEstadisticas($fecha_inicio_mes, $fecha_fin_mes);
$stats_creditos = $creditoModel->obtenerEstadisticas();
$stats_inventario = $iphoneModel->obtenerEstadisticas();
$stats_clientes = $clienteModel->obtenerEstadisticasGenerales();
$stats_apartados = $apartadoModel->obtenerEstadisticas();

// Créditos en mora
$creditos_mora = $creditoModel->obtenerEnMora();

// Próximos vencimientos
$proximos_vencimientos = $creditoModel->obtenerProximosVencimientos(7);

// Alertas de stock bajo
$alertas_stock = $iphoneModel->obtenerAlertasStock(3);

// Ventas recientes
$ventas_recientes = $ventaModel->obtenerTodos(['limit' => 5]);

include __DIR__ . '/layouts/header.php';
?>

<div class="dashboard-page">
    <!-- Hero Header Apple Style -->
    <div class="apple-hero-section mb-5">
        <div class="apple-greeting">
            <h1 class="apple-main-title">Hola, <?php echo htmlspecialchars(explode(' ', $usuario['nombre'])[0]); ?></h1>
            <p class="apple-subtitle">Resumen de tu negocio de iPhones</p>
        </div>
        <div class="apple-period-selector">
            <button type="button" class="apple-period-btn" id="btnHoy" title="Estadísticas de hoy">Hoy</button>
            <button type="button" class="apple-period-btn active" id="btnMes" title="Estadísticas del mes actual">Este Mes</button>
            <button type="button" class="apple-period-btn" id="btnAno" title="Estadísticas del año">Este Año</button>
        </div>
    </div>

    <!-- Apple Stats Cards Grid -->
    <div class="apple-stats-grid mb-5">
        <!-- Sales Card -->
        <div class="apple-stat-card">
            <div class="apple-stat-icon apple-icon-sales">
                <i class="bi bi-bag-check"></i>
            </div>
            <div class="apple-stat-content">
                <p class="apple-stat-label">Ventas</p>
                <h2 class="apple-stat-value"><?php echo formatearMoneda($stats_ventas['total_vendido'] ?? 0); ?></h2>
                <p class="apple-stat-meta"><?php echo $stats_ventas['total_ventas'] ?? 0; ?> transacciones</p>
            </div>
        </div>

        <!-- Receivables Card -->
        <div class="apple-stat-card">
            <div class="apple-stat-icon apple-icon-receivables">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="apple-stat-content">
                <p class="apple-stat-label">Por Cobrar</p>
                <h2 class="apple-stat-value"><?php echo formatearMoneda($stats_creditos['saldo_por_cobrar'] ?? 0); ?></h2>
                <p class="apple-stat-meta"><?php echo $stats_creditos['activos'] ?? 0; ?> créditos</p>
            </div>
        </div>

        <!-- Inventory Card -->
        <div class="apple-stat-card">
            <div class="apple-stat-icon apple-icon-inventory">
                <i class="bi bi-phone"></i>
            </div>
            <div class="apple-stat-content">
                <p class="apple-stat-label">Inventario</p>
                <h2 class="apple-stat-value"><?php echo $stats_inventario['disponibles'] ?? 0; ?></h2>
                <p class="apple-stat-meta"><?php echo formatearMoneda($stats_inventario['valor_inventario_venta'] ?? 0); ?> valor</p>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="apple-stat-card">
            <div class="apple-stat-icon apple-icon-customers">
                <i class="bi bi-people"></i>
            </div>
            <div class="apple-stat-content">
                <p class="apple-stat-label">Clientes</p>
                <h2 class="apple-stat-value"><?php echo $stats_clientes['activos'] ?? 0; ?></h2>
                <p class="apple-stat-meta"><?php echo $stats_clientes['morosos'] ?? 0; ?> en mora</p>
            </div>
        </div>

        <!-- Apartados Card -->
        <div class="apple-stat-card">
            <div class="apple-stat-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                <i class="bi bi-bookmark-check"></i>
            </div>
            <div class="apple-stat-content">
                <p class="apple-stat-label">Apartados</p>
                <h2 class="apple-stat-value"><?php echo $stats_apartados['activos'] ?? 0; ?></h2>
                <p class="apple-stat-meta"><?php echo $stats_apartados['vencidos'] ?? 0; ?> vencidos</p>
            </div>
        </div>
    </div>

    <!-- Apple Alerts Section -->
    <?php if (count($creditos_mora) > 0 || count($alertas_stock) > 0 || count($proximos_vencimientos) > 0): ?>
    <div class="apple-alerts-section mb-5">
        <h3 class="apple-section-title">Alertas Importantes</h3>
        <div class="apple-alerts-grid">
            <!-- Overdue Credits -->
            <?php if (count($creditos_mora) > 0): ?>
            <div class="apple-alert apple-alert-danger">
                <div class="apple-alert-icon">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="apple-alert-content">
                    <h4>Créditos en Mora</h4>
                    <p><?php echo count($creditos_mora); ?> crédito(s) vencido(s) requieren atención inmediata.</p>
                    <a href="<?php echo BASE_URL; ?>/views/creditos/mora.php" class="apple-alert-link">Ver Detalles →</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Low Stock -->
            <?php if (count($alertas_stock) > 0): ?>
            <div class="apple-alert apple-alert-warning">
                <div class="apple-alert-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="apple-alert-content">
                    <h4>Stock Bajo</h4>
                    <p><?php echo count($alertas_stock); ?> modelo(s) tienen inventario bajo o agotado.</p>
                    <a href="<?php echo BASE_URL; ?>/views/inventario/lista.php" class="apple-alert-link">Ver Inventario →</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Upcoming Payments -->
            <?php if (count($proximos_vencimientos) > 0): ?>
            <div class="apple-alert apple-alert-info">
                <div class="apple-alert-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="apple-alert-content">
                    <h4>Próximos Vencimientos</h4>
                    <p><?php echo count($proximos_vencimientos); ?> cuota(s) vencen esta semana.</p>
                    <a href="<?php echo BASE_URL; ?>/views/creditos/lista.php" class="apple-alert-link">Ver Créditos →</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content Grid: Recent Sales & Analytics -->
    <div class="apple-content-grid">
        <!-- Recent Sales Card -->
        <div class="apple-card apple-card-large">
            <div class="apple-card-header">
                <h3>Ventas Recientes</h3>
                <a href="<?php echo BASE_URL; ?>/views/ventas/lista.php" class="apple-card-link">Ver todo →</a>
            </div>
            <div class="apple-table-container">
                <table class="apple-table">
                    <thead>
                        <tr>
                            <th>N° Venta</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Total</th>
                            <th class="apple-table-hidden">Fecha</th>
                            <th class="apple-table-actions">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($ventas_recientes) > 0): ?>
                            <?php foreach (array_slice($ventas_recientes, 0, 5) as $venta): ?>
                            <tr>
                                <td><span class="apple-badge-number"><?php echo htmlspecialchars($venta['numero_venta']); ?></span></td>
                                <td><span class="apple-text-primary"><?php echo htmlspecialchars($venta['cliente_nombre']); ?></span></td>
                                <td>
                                    <span class="apple-badge <?php echo $venta['tipo_venta'] == 'contado' ? 'apple-badge-success' : 'apple-badge-warning'; ?>">
                                        <?php echo ucfirst($venta['tipo_venta']); ?>
                                    </span>
                                </td>
                                <td><span class="apple-text-success"><?php echo formatearMoneda($venta['total']); ?></span></td>
                                <td class="apple-table-hidden"><?php echo formatearFechaHora($venta['fecha_venta']); ?></td>
                                <td class="apple-table-actions">
                                    <a href="<?php echo BASE_URL; ?>/views/ventas/detalle.php?id=<?php echo $venta['id']; ?>" class="apple-btn-icon" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="apple-empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No hay ventas registradas</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Analytics Sidebar -->
        <div class="apple-analytics-sidebar">
            <div class="apple-sidebar-row">
                <!-- Monthly Summary -->
                <div class="apple-card apple-card-small">
                    <div class="apple-card-header">
                        <h4>Resumen del Mes</h4>
                    </div>
                    <div class="apple-stats-breakdown">
                        <div class="apple-stat-row">
                            <span class="apple-stat-label">Ventas Contado</span>
                            <span class="apple-stat-number"><?php echo formatearMoneda($stats_ventas['total_contado'] ?? 0); ?></span>
                        </div>
                        <div class="apple-progress-bar">
                            <div class="apple-progress-fill" style="width: <?php echo ($stats_ventas['total_vendido'] > 0) ? ($stats_ventas['total_contado'] / $stats_ventas['total_vendido'] * 100) : 0; ?>%;"></div>
                        </div>

                        <div class="apple-stat-row mt-4">
                            <span class="apple-stat-label">Ventas Crédito</span>
                            <span class="apple-stat-number"><?php echo formatearMoneda($stats_ventas['total_credito'] ?? 0); ?></span>
                        </div>
                        <div class="apple-progress-bar">
                            <div class="apple-progress-fill" style="width: <?php echo ($stats_ventas['total_vendido'] > 0) ? ($stats_ventas['total_credito'] / $stats_ventas['total_vendido'] * 100) : 0; ?>%;"></div>
                        </div>

                        <hr class="apple-divider">

                        <div class="apple-stat-row">
                            <span class="apple-stat-label">Ticket Promedio</span>
                            <span class="apple-stat-number"><?php echo formatearMoneda($stats_ventas['ticket_promedio'] ?? 0); ?></span>
                        </div>

                        <div class="apple-stat-row">
                            <span class="apple-stat-label">Total Intereses</span>
                            <span class="apple-stat-number apple-text-green"><?php echo formatearMoneda($stats_creditos['total_intereses_generados'] ?? 0); ?></span>
                        </div>

                        <div class="apple-stat-row">
                            <span class="apple-stat-label">iPhones Vendidos</span>
                            <span class="apple-stat-number"><?php echo ($stats_inventario['vendidos'] + $stats_inventario['en_credito']) ?? 0; ?> unidades</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="apple-card apple-card-small">
                    <div class="apple-card-header">
                        <h4>Acciones Rápidas</h4>
                    </div>
                    <div class="apple-quick-actions">
                        <a href="<?php echo BASE_URL; ?>/views/ventas/nueva.php" class="apple-action-btn">
                            <i class="bi bi-bag-plus"></i>
                            <span>Nueva Venta</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/views/creditos/pagos.php" class="apple-action-btn">
                            <i class="bi bi-coin"></i>
                            <span>Registrar Pago</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/views/clientes/nuevo.php" class="apple-action-btn">
                            <i class="bi bi-person-plus"></i>
                            <span>Nuevo Cliente</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php" class="apple-action-btn">
                            <i class="bi bi-phone"></i>
                            <span>Agregar iPhone</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
