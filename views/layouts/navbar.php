<?php
/**
 * Barra de Navegación Principal - Versión 2026
 * Navegación profesional horizontal
 */
?>

<!-- Navegación Secundaria - Debajo del Header Principal -->
<nav class="navbar-secondary">
    <div class="nav-container">
        <!-- Logo comprimido -->
        <a href="<?php echo BASE_URL; ?>/views/dashboard.php" class="nav-logo">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <!-- Menú Principal Desktop -->
        <ul class="nav-menu">
            <!-- Ventas -->
            <li class="nav-menu-item dropdown-menu-item">
                <a href="#" class="nav-link-main">
                    <i class="bi bi-cart"></i> Ventas
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>/views/ventas/nueva.php">
                        <i class="bi bi-cart-plus"></i> Nueva Venta
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/ventas/lista.php">
                        <i class="bi bi-receipt"></i> Historial
                    </a>
                </div>
            </li>

            <!-- Créditos -->
            <li class="nav-menu-item dropdown-menu-item">
                <a href="#" class="nav-link-main">
                    <i class="bi bi-credit-card"></i> Créditos
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>/views/creditos/lista.php">
                        <i class="bi bi-credit-card"></i> Activos
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/creditos/mora.php">
                        <i class="bi bi-exclamation-triangle"></i> En Mora
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/creditos/pagos.php">
                        <i class="bi bi-cash-coin"></i> Registrar Pago
                    </a>
                </div>
            </li>

            <!-- Inventario -->
            <li class="nav-menu-item dropdown-menu-item">
                <a href="#" class="nav-link-main">
                    <i class="bi bi-box"></i> Inventario
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>/views/inventario/lista.php">
                        <i class="bi bi-phone"></i> iPhones
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php">
                        <i class="bi bi-plus-circle"></i> Agregar
                    </a>
                </div>
            </li>

            <!-- Gestión -->
            <li class="nav-menu-item dropdown-menu-item">
                <a href="#" class="nav-link-main">
                    <i class="bi bi-person"></i> Gestión
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>/views/clientes/lista.php">
                        <i class="bi bi-people"></i> Clientes
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/proveedores/lista.php">
                        <i class="bi bi-building"></i> Proveedores
                    </a>
                </div>
            </li>

            <!-- Reportes -->
            <li class="nav-menu-item dropdown-menu-item">
                <a href="#" class="nav-link-main">
                    <i class="bi bi-graph-up"></i> Reportes
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>/views/reportes/ventas.php">
                        <i class="bi bi-receipt"></i> Ventas
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/reportes/creditos.php">
                        <i class="bi bi-credit-card"></i> Créditos
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/reportes/ganancias.php">
                        <i class="bi bi-graph-up"></i> Ganancias
                    </a>
                </div>
            </li>

            <!-- Administración -->
            <li class="nav-menu-item dropdown-menu-item">
                <a href="#" class="nav-link-main">
                    <i class="bi bi-gear"></i> Admin
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>/views/usuarios/lista.php">
                        <i class="bi bi-person-check"></i> Usuarios
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/configuracion.php">
                        <i class="bi bi-sliders"></i> Configuración
                    </a>
                </div>
            </li>
        </ul>

        <!-- Mobile Toggle -->
        <button class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <!-- Menú Mobile -->
    <div class="nav-menu-mobile" id="navMenuMobile">
        <a href="<?php echo BASE_URL; ?>/views/dashboard.php" class="nav-mobile-item">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="<?php echo BASE_URL; ?>/views/ventas/nueva.php" class="nav-mobile-item">
            <i class="bi bi-cart-plus"></i> Nueva Venta
        </a>
        <a href="<?php echo BASE_URL; ?>/views/creditos/lista.php" class="nav-mobile-item">
            <i class="bi bi-credit-card"></i> Créditos
        </a>
        <a href="<?php echo BASE_URL; ?>/views/inventario/lista.php" class="nav-mobile-item">
            <i class="bi bi-phone"></i> Inventario
        </a>
        <a href="<?php echo BASE_URL; ?>/views/clientes/lista.php" class="nav-mobile-item">
            <i class="bi bi-people"></i> Clientes
        </a>
        <a href="<?php echo BASE_URL; ?>/views/reportes/ventas.php" class="nav-mobile-item">
            <i class="bi bi-graph-up"></i> Reportes
        </a>
        <hr class="nav-divider">
        <a href="<?php echo BASE_URL; ?>/views/configuracion.php" class="nav-mobile-item">
            <i class="bi bi-sliders"></i> Configuración
        </a>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const navMenuMobile = document.getElementById('navMenuMobile');

    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenuMobile.classList.toggle('active');
            navToggle.classList.toggle('active');
        });

        // Cerrar menú al hacer click en un link
        const mobileItems = navMenuMobile.querySelectorAll('.nav-mobile-item');
        mobileItems.forEach(item => {
            item.addEventListener('click', function() {
                navMenuMobile.classList.remove('active');
                navToggle.classList.remove('active');
            });
        });

        // Cerrar menú al hacer click fuera
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.navbar-secondary')) {
                navMenuMobile.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    }

    // Desktop dropdowns
    const dropdownItems = document.querySelectorAll('.dropdown-menu-item');
    dropdownItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const dropdown = this.querySelector('.dropdown-content');
            if (dropdown) dropdown.classList.add('show');
        });

        item.addEventListener('mouseleave', function() {
            const dropdown = this.querySelector('.dropdown-content');
            if (dropdown) dropdown.classList.remove('show');
        });
    });
});
</script>
