<?php
/**
 * Header del sistema - Versión Mejorada 2026
 * Incluir en todas las páginas internas
 */

// Verificar autenticación
if (!estaLogueado()) {
    redirect('/views/login.php');
}

$usuario = usuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo $page_title ?? APP_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- Custom CSS - DESPUÉS de Bootstrap para sobreescribir -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css?v=3.0">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/header.css?v=3.0">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/mobile.css?v=3.0">
    
    <style>
        /* Additional mobile optimizations */
        @supports (padding: max(0px)) {
            body {
                padding-left: max(0px, env(safe-area-inset-left));
                padding-right: max(0px, env(safe-area-inset-right));
            }
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="tel"],
        select,
        textarea {
            font-size: 16px !important;
        }
    </style>
    <!-- Dark mode removed: simplified header scripts -->
</head>
<body>
    <!-- Header Principal - Apple Minimalist Style -->
    <nav class="navbar sticky-top app-header">
        <div class="container-fluid navbar-container">
            <div class="d-flex align-items-center justify-content-between w-100 gap-3">
                
                <!-- Left: Logo (Mobile Hamburger + Desktop Logo) -->
                <div class="navbar-left d-flex align-items-center gap-3">
                    <!-- Hamburger Menu (Always Visible) -->
                    <button class="btn hamburger-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="#mobileMenu" title="Menú">
                        <i class="bi bi-list"></i>
                    </button>

                    <!-- Logo y Brand -->
                    <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/views/dashboard.php" title="Ir al Dashboard">
                        <div class="logo-box">
                            <i class="bi bi-apple"></i>
                        </div>
                        <span class="brand-text d-none d-md-inline">GoApple</span>
                    </a>
                </div>

                <!-- Center: Navigation Menu (Desktop Only) -->
                <nav class="navbar-menu d-none d-md-flex align-items-center">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/views/dashboard.php">Dashboard</a>
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/views/ventas/lista.php">Ventas</a>
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/views/clientes/lista.php">Clientes</a>
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/views/inventario/lista.php">Inventario</a>
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/views/creditos/lista.php">Créditos</a>
                </nav>
                
                <!-- Right: Actions (Search + Buttons) -->
                <div class="navbar-right d-flex align-items-center gap-2">
                    <!-- Search Bar (Desktop) -->
                    <div class="header-search position-relative d-none d-md-flex">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="globalSearch" placeholder="Buscar..." autocomplete="off" />
                        </div>
                        <div id="searchResults" class="position-absolute search-dropdown" style="top: 100%; right: 0; z-index: 1051;"></div>
                    </div>

                    <!-- Desktop Action Buttons -->
                    <div class="d-none d-lg-flex align-items-center gap-1">
                        <!-- Quick Actions -->
                        <div class="dropdown">
                            <button class="btn btn-action" type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Acciones Rápidas">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end quick-menu" aria-labelledby="quickActionsDropdown">
                                <li><h6 class="dropdown-header">Acciones Rápidas</h6></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/ventas/nueva.php"><i class="bi bi-cart-plus me-2"></i> Nueva Venta</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/clientes/nuevo.php"><i class="bi bi-person-plus me-2"></i> Nuevo Cliente</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php"><i class="bi bi-plus-square me-2"></i> Nuevo Producto</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/creditos/lista.php"><i class="bi bi-cash-coin me-2"></i> Créditos</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/creditos/pagos.php"><i class="bi bi-wallet2 me-2"></i> Pagos</a></li>
                                <li><hr class="dropdown-divider my-1"></li>

                            </ul>
                        </div>

                        <!-- Notificaciones -->
                        <div class="dropdown">
                            <button class="btn btn-action position-relative" type="button" id="notificacionesDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count">3</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end notif-menu" aria-labelledby="notificacionesDropdown">
                                <li><h6 class="dropdown-header">Notificaciones</h6></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item notif-item" href="<?php echo BASE_URL; ?>/views/creditos/mora.php">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="notif-avatar bg-danger bg-opacity-10">
                                                <i class="bi bi-exclamation-triangle text-danger"></i>
                                            </div>
                                            <div class="flex-grow-1 notif-text">
                                                <div class="title">Créditos en mora</div>
                                                <div class="meta">5 clientes con pagos pendientes</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item notif-item" href="<?php echo BASE_URL; ?>/views/inventario/lista.php">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="notif-avatar bg-warning bg-opacity-10">
                                                <i class="bi bi-box-seam text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1 notif-text">
                                                <div class="title">Stock bajo</div>
                                                <div class="meta">3 productos requieren reposición</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item notif-item" href="<?php echo BASE_URL; ?>/views/ventas/lista.php">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="notif-avatar bg-success bg-opacity-10">
                                                <i class="bi bi-check-circle text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 notif-text">
                                                <div class="title">Nueva venta registrada</div>
                                                <div class="meta">iPhone 13 Pro vendido exitosamente</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item text-center text-primary" href="#">Ver todas</a></li>
                            </ul>
                        </div>

                        <!-- Perfil Usuario -->
                        <div class="dropdown">
                            <button class="btn btn-action" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Perfil">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end user-menu" aria-labelledby="userDropdown">
                                <li class="px-3 py-2">
                                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
                                    <div class="small text-muted"><?php echo ucfirst($usuario['rol']); ?></div>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/configuracion.php"><i class="bi bi-gear me-2"></i> Configuración</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/views/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Mobile Actions (Search + User) -->
                    <div class="d-flex d-lg-none align-items-center gap-2">
                        <button class="btn btn-action" type="button" data-bs-toggle="modal" data-bs-target="#mobileSearchModal">
                            <i class="bi bi-search"></i>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-action" type="button" id="mobileUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end mobile-user-menu" aria-labelledby="mobileUserDropdown">
                                <li class="px-3 py-2">
                                    <div class="fw-semibold"><?php echo htmlspecialchars(explode(' ', $usuario['nombre'])[0]); ?></div>
                                    <div class="small text-muted"><?php echo ucfirst($usuario['rol']); ?></div>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/configuracion.php"><i class="bi bi-gear me-2"></i> Configuración</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/views/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- DESKTOP SIDEBAR - HIDDEN (Not used anymore) -->
    <aside class="sidebar d-none">
        <div class="sidebar-wrapper">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="bi bi-apple"></i>
                    <span>GoApple</span>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                <!-- PRINCIPAL -->
                <div class="nav-group">
                    <div class="nav-group-title">PRINCIPAL</div>
                    <a href="<?php echo BASE_URL; ?>/views/dashboard.php" class="nav-item">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- VENTAS -->
                <div class="nav-group">
                    <div class="nav-group-title">VENTAS</div>
                    <a href="<?php echo BASE_URL; ?>/views/ventas/lista.php" class="nav-item">
                        <i class="bi bi-receipt"></i>
                        <span>Historial</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/ventas/nueva.php" class="nav-item">
                        <i class="bi bi-cart-plus"></i>
                        <span>Nueva Venta</span>
                    </a>
                </div>

                <!-- CLIENTES -->
                <div class="nav-group">
                    <div class="nav-group-title">CLIENTES</div>
                    <a href="<?php echo BASE_URL; ?>/views/clientes/lista.php" class="nav-item">
                        <i class="bi bi-people"></i>
                        <span>Lista</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/clientes/nuevo.php" class="nav-item">
                        <i class="bi bi-person-plus"></i>
                        <span>Nuevo</span>
                    </a>
                </div>

                <!-- INVENTARIO -->
                <div class="nav-group">
                    <div class="nav-group-title">INVENTARIO</div>
                    <a href="<?php echo BASE_URL; ?>/views/inventario/lista.php" class="nav-item">
                        <i class="bi bi-box-seam"></i>
                        <span>Productos</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php" class="nav-item">
                        <i class="bi bi-plus-square"></i>
                        <span>Nuevo</span>
                    </a>
                </div>

                <!-- CRÉDITOS -->
                <div class="nav-group">
                    <div class="nav-group-title">CRÉDITOS</div>
                    <a href="<?php echo BASE_URL; ?>/views/creditos/lista.php" class="nav-item">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Créditos</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/creditos/mora.php" class="nav-item">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>En Mora</span>
                    </a>
                </div>

                <!-- REPORTES -->
                <div class="nav-group">
                    <div class="nav-group-title">REPORTES</div>
                    <a href="<?php echo BASE_URL; ?>/views/reportes/ventas.php" class="nav-item">
                        <i class="bi bi-graph-up"></i>
                        <span>Ventas</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/reportes/ganancias.php" class="nav-item">
                        <i class="bi bi-cash-coin"></i>
                        <span>Ganancias</span>
                    </a>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <a href="<?php echo BASE_URL; ?>/views/configuracion.php" class="nav-item">
                    <i class="bi bi-gear"></i>
                    <span>Configuración</span>
                </a>
                <a href="<?php echo BASE_URL; ?>/views/logout.php" class="nav-item text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Salir</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- MOBILE OFFCANVAS - IDENTICAL TO QUICK ACTIONS -->
    <div class="offcanvas offcanvas-start mobile-offcanvas" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header offcanvas-header-custom">
            <h5 class="offcanvas-title fw-bold offcanvas-title-custom" id="mobileMenuLabel">
                <i class="bi bi-apple offcanvas-apple-icon"></i>Menú
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 offcanvas-body-custom">
            <ul class="dropdown-menu dropdown-menu-start mobile-quick-menu w-100" style="display: block !important; position: static !important; border: none !important; box-shadow: none !important;">
                
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/ventas/lista.php"><i class="bi bi-receipt me-2"></i> Ventas</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/ventas/nueva.php"><i class="bi bi-cart-plus me-2"></i> Nueva Venta</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/clientes/lista.php"><i class="bi bi-people me-2"></i> Clientes</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/clientes/nuevo.php"><i class="bi bi-person-plus me-2"></i> Nuevo Cliente</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/inventario/lista.php"><i class="bi bi-box-seam me-2"></i> Inventario</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php"><i class="bi bi-plus-square me-2"></i> Nuevo Producto</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/creditos/lista.php"><i class="bi bi-file-earmark-text me-2"></i> Créditos</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/creditos/mora.php"><i class="bi bi-exclamation-triangle me-2"></i> Créditos en Mora</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/creditos/pagos.php"><i class="bi bi-wallet2 me-2"></i> Pagos</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/proveedores/lista.php"><i class="bi bi-truck me-2"></i> Proveedores</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/proveedores/nuevo.php"><i class="bi bi-truck-front me-2"></i> Nuevo Proveedor</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/usuarios/lista.php"><i class="bi bi-person-badge me-2"></i> Usuarios</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/usuarios/nuevo.php"><i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/reportes/ventas.php"><i class="bi bi-graph-up me-2"></i> Reporte de Ventas</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/reportes/creditos.php"><i class="bi bi-bar-chart-line me-2"></i> Reporte de Créditos</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/reportes/ganancias.php"><i class="bi bi-pie-chart me-2"></i> Reporte de Ganancias</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/configuracion.php"><i class="bi bi-gear me-2"></i> Configuración</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/views/perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
            </ul>
        </div>
    </div>

    <!-- Mobile Search Modal -->
    <div class="modal fade" id="mobileSearchModal" tabindex="-1" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <div class="input-group">
                        <span class="input-group-text mobile-search-icon">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control mobile-search-input" id="mobileGlobalSearch" placeholder="Buscar productos, clientes, ventas..." autofocus />
                    </div>
                    <button type="button" class="btn-close ms-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <div id="mobileSearchResults"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-wrapper">
        <!-- Desktop Sidebar se muestra en d-lg-block -->
        <!-- Mobile Offcanvas funciona con data-bs-toggle -->
        
        <!-- Main Content Area -->
        <main class="main-content">
            <div class="content-container">
                <?php
                // Mostrar mensajes flash con animación
                $flash = getFlashMessage();
                if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['tipo']; ?> alert-dismissible fade show slide-in-up" role="alert">
                    <?php
                    $icon = match($flash['tipo']) {
                        'success' => 'check-circle-fill',
                        'error', 'danger' => 'exclamation-triangle-fill',
                        'warning' => 'exclamation-circle-fill',
                        'info' => 'info-circle-fill',
                        default => 'info-circle-fill'
                    };
                    ?>
                    <i class="bi bi-<?php echo $icon; ?> me-2"></i>
                    <?php echo htmlspecialchars($flash['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
                <?php endif; ?>
