<nav id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/dashboard.php" title="Dashboard">
                    <i class="bi bi-speedometer2"></i> 
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- Ventas -->
            <li class="nav-item">
                <h6 class="sidebar-heading">
                    <i class="bi bi-cart"></i> <span class="heading-text">Ventas</span>
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'ventas/nueva.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/ventas/nueva.php" title="Nueva Venta">
                    <i class="bi bi-cart-plus"></i> 
                    <span class="nav-text">Nueva Venta</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'ventas/lista.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/ventas/lista.php" title="Historial de Ventas">
                    <i class="bi bi-receipt"></i> 
                    <span class="nav-text">Historial</span>
                </a>
            </li>

            <!-- Créditos -->
            <li class="nav-item">
                <h6 class="sidebar-heading">
                    <i class="bi bi-credit-card"></i> <span class="heading-text">Créditos</span>
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'creditos/lista.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/creditos/lista.php" title="Créditos Activos">
                    <i class="bi bi-credit-card"></i> 
                    <span class="nav-text">Activos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'creditos/mora.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/creditos/mora.php" title="Créditos en Mora">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <span class="nav-text">En Mora</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'creditos/pagos.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/creditos/pagos.php" title="Registrar Pago">
                    <i class="bi bi-cash-coin"></i> 
                    <span class="nav-text">Registrar Pago</span>
                </a>
            </li>

            <!-- Inventario -->
            <li class="nav-item">
                <h6 class="sidebar-heading">
                    <i class="bi bi-box"></i> <span class="heading-text">Inventario</span>
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'inventario/lista.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/inventario/lista.php" title="iPhones en Inventario">
                    <i class="bi bi-phone"></i> 
                    <span class="nav-text">iPhones</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'inventario/nuevo.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php" title="Agregar iPhone">
                    <i class="bi bi-plus-circle"></i> 
                    <span class="nav-text">Agregar</span>
                </a>
            </li>

            <!-- Gestión -->
            <li class="nav-item">
                <h6 class="sidebar-heading">
                    <i class="bi bi-person"></i> <span class="heading-text">Gestión</span>
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'clientes/lista.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/clientes/lista.php" title="Gestionar Clientes">
                    <i class="bi bi-people"></i> 
                    <span class="nav-text">Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'proveedores/lista.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/proveedores/lista.php" title="Gestionar Proveedores">
                    <i class="bi bi-building"></i> 
                    <span class="nav-text">Proveedores</span>
                </a>
            </li>

            <!-- Reportes -->
            <li class="nav-item">
                <h6 class="sidebar-heading">
                    <i class="bi bi-graph-up"></i> <span class="heading-text">Reportes</span>
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'reportes/ventas.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/reportes/ventas.php" title="Reporte de Ventas">
                    <i class="bi bi-graph-up"></i> 
                    <span>Ventas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'reportes/creditos.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/reportes/creditos.php" title="Reporte de Créditos">
                    <i class="bi bi-file-earmark-bar-graph"></i> 
                    <span>Créditos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'reportes/ganancias.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/reportes/ganancias.php" title="Reporte de Ganancias">
                    <i class="bi bi-currency-dollar"></i> 
                    <span>Ganancias</span>
                </a>
            </li>

            <?php if (esAdmin()): ?>
            <!-- Administración (solo admin) -->
            <li class="nav-item">
                <h6 class="sidebar-heading">
                    <i class="bi bi-lock"></i> Administración
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'usuarios/lista.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/usuarios/lista.php" title="Gestionar Usuarios">
                    <i class="bi bi-person-badge"></i> 
                    <span>Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'configuracion.php') !== false ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/views/configuracion.php" title="Configuración del Sistema">
                    <i class="bi bi-gear"></i> 
                    <span>Configuración</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
