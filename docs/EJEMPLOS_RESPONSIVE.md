# 🎯 EJEMPLOS DE VISTAS RESPONSIVE

## Ejemplo 1: Formulario de Nueva Venta (Adaptado)

```html
<!-- views/ventas/nueva.php - SECCIÓN DE FORMULARIO ADAPTADA -->

<div class="container-fluid py-4">
    <h1><i class="bi bi-cart-plus"></i> Nueva Venta</h1>
    
    <!-- PASO 1: Cliente -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(0,113,227,0.3); border-radius: 50%; margin-right: 0.5rem;">1</span>
                Seleccionar Cliente
            </h5>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Cliente</label>
                <select class="form-select select2" name="cliente_id" required>
                    <option value="">-- Seleccionar cliente --</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id']; ?>">
                            <?php echo htmlspecialchars($cliente['nombre']); ?> - <?php echo htmlspecialchars($cliente['documento']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- PASO 2: Tipo de Venta -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(0,113,227,0.3); border-radius: 50%; margin-right: 0.5rem;">2</span>
                Tipo de Venta
            </h5>
        </div>
        <div class="card-body">
            <div class="form-row cols-2">  <!-- 2 columnas en desktop, 1 en móvil -->
                <div class="form-group">
                    <label class="form-label">Tipo de Venta</label>
                    <select class="form-select" name="tipo_venta" id="tipoVenta" required>
                        <option value="contado">Contado</option>
                        <option value="credito">Crédito</option>
                    </select>
                </div>
                <div class="form-group" id="grupoTasaInteres" style="display: none;">
                    <label class="form-label">Tasa de Interés (%)</label>
                    <input type="number" class="form-control" name="tasa_interes" step="0.01" min="0" max="100">
                </div>
            </div>
        </div>
    </div>

    <!-- PASO 3: Productos -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(0,113,227,0.3); border-radius: 50%; margin-right: 0.5rem;">3</span>
                Seleccionar iPhones
            </h5>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">iPhone</label>
                <select class="form-select select2" name="iphone_id" id="iphoneSelect" required>
                    <option value="">-- Seleccionar iPhone --</option>
                    <?php foreach ($iphones as $iphone): ?>
                        <option value="<?php echo $iphone['id']; ?>" 
                                data-precio="<?php echo $iphone['precio_venta']; ?>">
                            <?php echo htmlspecialchars($iphone['modelo']); ?> - 
                            <?php echo htmlspecialchars($iphone['capacidad']); ?> - 
                            <?php echo htmlspecialchars($iphone['color']); ?>
                            (<?php echo formatearMoneda($iphone['precio_venta']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row cols-2">
                <div class="form-group">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidadInput" min="1" value="1">
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-primary btn-lg" id="agregarBtn">
                        <i class="bi bi-plus-circle"></i> Agregar
                    </button>
                </div>
            </div>

            <!-- Tabla de productos agregados (responsiva) -->
            <div class="table-responsive mt-4">
                <table class="table" id="productosTable">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="productosTableBody">
                        <!-- Se llenarán con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Resumen y Fotos -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(0,113,227,0.3); border-radius: 50%; margin-right: 0.5rem;">4</span>
                Fotos y Detalles
            </h5>
        </div>
        <div class="card-body">
            <!-- Fotos -->
            <div class="form-group mb-4">
                <?php 
                $id_zona = 'venta';
                $tipo_fotos = 'de la Venta';
                include __DIR__ . '/../components/fotos-upload.php'; 
                ?>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" name="observaciones" 
                          placeholder="Notas adicionales sobre la venta..."></textarea>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn btn-success btn-lg" form="formVenta">
            <i class="bi bi-check-circle"></i> Crear Venta
        </button>
        <a href="<?php echo BASE_URL; ?>/views/ventas/lista.php" class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-x-circle"></i> Cancelar
        </a>
    </div>
</div>
```

---

## Ejemplo 2: Listado de Inventario (Adaptado)

```html
<!-- views/inventario/lista.php - SECCIÓN DE TABLA ADAPTADA -->

<div class="container-fluid py-4">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap;">
        <h1><i class="bi bi-phone"></i> Inventario de iPhones</h1>
        <a href="<?php echo BASE_URL; ?>/views/inventario/nuevo.php" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle"></i> Agregar iPhone
        </a>
    </div>

    <!-- Tabla responsiva -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">iPhones Disponibles</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Modelo</th>
                            <th>Capacidad</th>
                            <th>Color</th>
                            <th>IMEI</th>
                            <th>Precio Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($iphones as $iphone): ?>
                        <tr>
                            <td data-label="Modelo">
                                <strong><?php echo htmlspecialchars($iphone['modelo']); ?></strong>
                            </td>
                            <td data-label="Capacidad"><?php echo htmlspecialchars($iphone['capacidad']); ?></td>
                            <td data-label="Color"><?php echo htmlspecialchars($iphone['color']); ?></td>
                            <td data-label="IMEI">
                                <code><?php echo htmlspecialchars($iphone['imei']); ?></code>
                            </td>
                            <td data-label="Precio" class="font-weight-bold text-success">
                                <?php echo formatearMoneda($iphone['precio_venta']); ?>
                            </td>
                            <td data-label="Estado">
                                <span class="badge badge-<?php 
                                    echo match($iphone['estado']) {
                                        'disponible' => 'success',
                                        'vendido' => 'danger',
                                        'en_credito' => 'warning',
                                        'apartado' => 'info',
                                        default => 'secondary'
                                    }
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $iphone['estado'])); ?>
                                </span>
                            </td>
                            <td data-label="Acciones">
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="<?php echo BASE_URL; ?>/views/inventario/ver.php?id=<?php echo $iphone['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/views/inventario/editar.php?id=<?php echo $iphone['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="eliminarIphone(<?php echo $iphone['id']; ?>)">
                                        <i class="bi bi-trash"></i> Eliminar
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
```

---

## Ejemplo 3: Formulario de Clientes (Adaptado)

```html
<!-- views/clientes/nuevo.php - FORMULARIO RESPONSIVE -->

<div class="container-fluid py-4">
    <h1><i class="bi bi-person-plus"></i> Nuevo Cliente</h1>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Información del Cliente</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>/views/clientes/nuevo.php">
                
                <!-- Fila 1: Nombre y Documento (2 columnas) -->
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" name="nombre" 
                               value="<?php echo htmlspecialchars($valores['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Documento (CC/NIT) *</label>
                        <input type="text" class="form-control" name="documento" 
                               value="<?php echo htmlspecialchars($valores['documento'] ?? ''); ?>" required>
                    </div>
                </div>

                <!-- Fila 2: Email y Teléfono (2 columnas) -->
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" 
                               value="<?php echo htmlspecialchars($valores['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" name="telefono" 
                               value="<?php echo htmlspecialchars($valores['telefono'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Fila 3: Dirección (completa) -->
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="direccion" 
                           value="<?php echo htmlspecialchars($valores['direccion'] ?? ''); ?>">
                </div>

                <!-- Fila 4: Ciudad y Departamento (2 columnas) -->
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Ciudad</label>
                        <input type="text" class="form-control" name="ciudad" 
                               value="<?php echo htmlspecialchars($valores['ciudad'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Departamento</label>
                        <input type="text" class="form-control" name="departamento" 
                               value="<?php echo htmlspecialchars($valores['departamento'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Botones -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save"></i> Guardar Cliente
                    </button>
                    <a href="<?php echo BASE_URL; ?>/views/clientes/lista.php" class="btn btn-outline-secondary btn-lg">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
```

---

## Ejemplo 4: Dashboard Responsivo

```html
<!-- views/dashboard.php - DASHBOARD ADAPTADO -->

<div class="container-fluid py-4">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>

    <!-- Tarjetas de Métricas (4 columnas en desktop, 2 en tablet, 1 en móvil) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Tarjeta: Ventas Hoy -->
        <div class="card hover-lift">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p class="text-muted" style="margin: 0; font-size: 0.9rem;">Ventas Hoy</p>
                        <h3 style="margin: 0; color: var(--color-primary);">
                            <?php echo formatearMoneda($ventasHoy); ?>
                        </h3>
                        <small class="text-muted"><?php echo $countVentasHoy; ?> transacciones</small>
                    </div>
                    <div style="font-size: 2rem; color: var(--color-primary); opacity: 0.5;">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Créditos Activos -->
        <div class="card hover-lift">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p class="text-muted" style="margin: 0; font-size: 0.9rem;">Créditos Activos</p>
                        <h3 style="margin: 0; color: var(--color-warning);">
                            <?php echo formatearMoneda($creditosActivos); ?>
                        </h3>
                        <small class="text-muted"><?php echo $countCreditos; ?> clientes</small>
                    </div>
                    <div style="font-size: 2rem; color: var(--color-warning); opacity: 0.5;">
                        <i class="bi bi-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Inventario -->
        <div class="card hover-lift">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p class="text-muted" style="margin: 0; font-size: 0.9rem;">iPhones Disponibles</p>
                        <h3 style="margin: 0; color: var(--color-success);">
                            <?php echo $countInventario; ?>
                        </h3>
                        <small class="text-muted">Stock en almacén</small>
                    </div>
                    <div style="font-size: 2rem; color: var(--color-success); opacity: 0.5;">
                        <i class="bi bi-phone"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Ganancias -->
        <div class="card hover-lift">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p class="text-muted" style="margin: 0; font-size: 0.9rem;">Ganancias Este Mes</p>
                        <h3 style="margin: 0; color: #34c759;">
                            <?php echo formatearMoneda($ganancias); ?>
                        </h3>
                        <small class="text-muted">+15% vs mes anterior</small>
                    </div>
                    <div style="font-size: 2rem; color: #34c759; opacity: 0.5;">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Gráficos (debajo, full-width) -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Ventas Últimos 30 Días</h5>
        </div>
        <div class="card-body">
            <canvas id="ventasChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>
```

---

## Notas Importantes

1. **Usa `form-row cols-2` para 2 columnas, `cols-3` para 3**
2. **Siempre envuelve tablas en `<div class="table-responsive">`**
3. **Las tarjetas usan `grid` con `minmax()` para ser responsivas automáticamente**
4. **Los botones de acción usa flex con wrap para adaptarse**
5. **Siempre incluye `responsive.css` antes de `style.css`**

Estos ejemplos son totalmente compatibles con tu backend PHP, solo necesitas adaptarlos a tus vistas específicas reemplazando las variables y lógica PHP.
