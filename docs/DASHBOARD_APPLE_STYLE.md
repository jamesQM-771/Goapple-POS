# 🎨 Dashboard Estilo Apple - HTML Template

Para que tu dashboard se vea profesional como Apple, usa este patrón en `views/dashboard.php`:

## Estructura HTML Recomendada

```html
<!-- Título Principal -->
<h1>
    <i class="bi bi-speedometer2"></i> Dashboard
</h1>
<p style="color: #6b7280; font-size: 0.95rem; margin-bottom: 2rem;">
    Bienvenido de nuevo, <?php echo htmlspecialchars(explode(' ', $usuario['nombre'])[0]); ?>!
</p>

<!-- GRID DE MÉTRICAS PRINCIPALES -->
<div class="metrics-grid">
    
    <!-- Métrica 1: Ventas Hoy -->
    <div class="card metric-card primary">
        <div class="metric-label">📊 Ventas Hoy</div>
        <div class="metric-value"><?php echo formatearMoneda($ventasHoy); ?></div>
        <div class="metric-subtitle"><?php echo $countVentasHoy; ?> transacciones</div>
    </div>

    <!-- Métrica 2: Créditos Activos -->
    <div class="card metric-card warning">
        <div class="metric-label">💰 Saldo por Cobrar</div>
        <div class="metric-value"><?php echo formatearMoneda($creditosActivos); ?></div>
        <div class="metric-subtitle"><?php echo $countCreditos; ?> créditos</div>
    </div>

    <!-- Métrica 3: Inventario -->
    <div class="card metric-card success">
        <div class="metric-label">📱 Inventario</div>
        <div class="metric-value"><?php echo $countInventario; ?> iPhones</div>
        <div class="metric-subtitle"><?php echo formatearMoneda($totalInventario); ?> en valor</div>
    </div>

    <!-- Métrica 4: Clientes -->
    <div class="card metric-card info">
        <div class="metric-label">👥 Clientes Activos</div>
        <div class="metric-value"><?php echo $countClientes; ?> clientes</div>
        <div class="metric-subtitle"><?php echo $clientesConCredito; ?> con crédito</div>
    </div>

</div>

<!-- SECCIÓN: Resumen Mensual -->
<div class="card">
    <div class="card-header">
        <h5>
            <i class="bi bi-graph-up"></i> Resumen del Mes
        </h5>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            
            <div>
                <p style="color: var(--color-gray); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    Total Vendido
                </p>
                <h3 style="margin: 0; color: var(--color-primary); font-size: 1.75rem;">
                    <?php echo formatearMoneda($ventasDelMes); ?>
                </h3>
                <small style="color: var(--color-gray);">
                    <?php echo $countVentasDelMes; ?> transacciones
                </small>
            </div>

            <div>
                <p style="color: var(--color-gray); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    Ganancias Estimadas
                </p>
                <h3 style="margin: 0; color: var(--color-success); font-size: 1.75rem;">
                    <?php echo formatearMoneda($gananciasDelMes); ?>
                </h3>
                <small style="color: var(--color-gray);">
                    <?php echo round($porcentajeGanancia, 1); ?>% margen
                </small>
            </div>

            <div>
                <p style="color: var(--color-gray); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    Créditos Pendientes
                </p>
                <h3 style="margin: 0; color: var(--color-warning); font-size: 1.75rem;">
                    <?php echo formatearMoneda($creditosPendientes); ?>
                </h3>
                <small style="color: var(--color-gray);">
                    <?php echo $creditosEnMora; ?> en mora
                </small>
            </div>

        </div>
    </div>
</div>

<!-- SECCIÓN: Acciones Rápidas -->
<div class="card">
    <div class="card-header">
        <h5>
            <i class="bi bi-lightning"></i> Acciones Rápidas
        </h5>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
            
            <a href="<?php echo BASE_URL; ?>/views/ventas/nueva.php" class="btn btn-primary btn-lg" style="width: 100%;">
                <i class="bi bi-cart-plus"></i> Nueva Venta
            </a>

            <a href="<?php echo BASE_URL; ?>/views/creditos/pagos.php" class="btn btn-success btn-lg" style="width: 100%;">
                <i class="bi bi-cash-coin"></i> Registrar Pago
            </a>

            <a href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php" class="btn btn-outline-secondary btn-lg" style="width: 100%;">
                <i class="bi bi-plus-circle"></i> Agregar iPhone
            </a>

            <a href="<?php echo BASE_URL; ?>/views/clientes/nuevo.php" class="btn btn-outline-secondary btn-lg" style="width: 100%;">
                <i class="bi bi-person-plus"></i> Nuevo Cliente
            </a>

        </div>
    </div>
</div>

<!-- SECCIÓN: Últimas Ventas (Tabla) -->
<div class="card">
    <div class="card-header">
        <h5>
            <i class="bi bi-receipt"></i> Últimas Ventas
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>iPhone</th>
                        <th>Monto</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimasVentas as $venta): ?>
                    <tr>
                        <td data-label="Cliente">
                            <?php echo htmlspecialchars($venta['cliente_nombre']); ?>
                        </td>
                        <td data-label="iPhone">
                            <?php echo htmlspecialchars($venta['modelo']); ?>
                        </td>
                        <td data-label="Monto" style="color: var(--color-primary); font-weight: 600;">
                            <?php echo formatearMoneda($venta['monto_total']); ?>
                        </td>
                        <td data-label="Tipo">
                            <span class="badge badge-<?php echo $venta['tipo_venta'] === 'credito' ? 'warning' : 'success'; ?>">
                                <?php echo ucfirst($venta['tipo_venta']); ?>
                            </span>
                        </td>
                        <td data-label="Fecha">
                            <?php echo date('d/m/Y', strtotime($venta['fecha_venta'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

## Clases CSS Disponibles

### Tarjetas de Métricas
```html
<div class="card metric-card primary">  <!-- primary, success, warning, info -->
    <div class="metric-label">Etiqueta</div>
    <div class="metric-value">$123.456</div>
    <div class="metric-subtitle">Descripción</div>
    <div class="metric-icon"><i class="bi bi-icon"></i></div>
</div>
```

### Grid de Métricas
```html
<div class="metrics-grid">
    <!-- Las tarjetas se adaptan automáticamente -->
</div>
```

### Tarjetas Estándar
```html
<div class="card">
    <div class="card-header">
        <h5>Título</h5>
    </div>
    <div class="card-body">
        Contenido
    </div>
</div>
```

## Características

✅ Diseño minimalista tipo Apple  
✅ Sombras suaves y profesionales  
✅ Animaciones fluidas en hover  
✅ Totalmente responsivo  
✅ Grid automático (4 columnas desktop, 2 tablet, 1 móvil)  
✅ Métricas grandes y legibles  
✅ Colores consistentes  
✅ Tipografía profesional

## Colores Disponibles para Métricas

- `primary` - Azul (#0071e3)
- `success` - Verde (#34c759)
- `warning` - Naranja (#ff9500)
- `info` - Cian (#06b6d4)

