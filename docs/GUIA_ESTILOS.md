# 📚 Guía de Estilos y Componentes - GoApple 2026

## Introducción

Este documento describe los estilos disponibles y cómo utilizarlos en nuevas vistas o componentes del sistema GoApple.

---

## 🎨 Colores Disponibles

### Variables CSS Globales

```css
--primary-color: #0071e3        (Azul principal)
--secondary-color: #34c759      (Verde)
--accent-color: #ff9500         (Naranja)
--danger-color: #ff3b30         (Rojo)
--warning-color: #ff9500        (Naranja claro)
--info-color: #00b4d8           (Azul claro)
```

### Grises

```css
--gray-50: #fafbfc      (Más claro - fondos)
--gray-100: #f5f6f8     
--gray-200: #e8ecf1     
--gray-300: #d9e0e9     
--gray-400: #c4cfe0     
--gray-500: #a0aec0     
--gray-600: #718096     
--gray-700: #4a5568     
--gray-800: #2d3748     
--gray-900: #1a202c     (Más oscuro - texto)
```

---

## 📦 Componentes Listos para Usar

### Cards

```html
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Título</h5>
    </div>
    <div class="card-body">
        Contenido aquí
    </div>
</div>
```

### Stat Cards

```html
<div class="stat-card h-100">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h6 style="color: #0071e3;">Titulo</h6>
            <div class="stat-value">$1,234.56</div>
            <div class="stat-subtitle">12 transacciones</div>
        </div>
        <div style="font-size: 2.8rem; color: #0071e3; opacity: 0.1;">
            <i class="bi bi-cart-check"></i>
        </div>
    </div>
</div>
```

### Botones

```html
<!-- Primario -->
<button class="btn btn-primary">Acción Principal</button>

<!-- Secundario -->
<button class="btn btn-secondary">Secundario</button>

<!-- Success -->
<button class="btn btn-success">Éxito</button>

<!-- Danger -->
<button class="btn btn-danger">Peligro</button>

<!-- Outline -->
<button class="btn btn-outline-primary">Outline</button>

<!-- Tamaños -->
<button class="btn btn-primary btn-sm">Pequeño</button>
<button class="btn btn-primary btn-lg">Grande</button>
```

### Formularios

```html
<div class="form-group">
    <label for="ejemplo" class="form-label">Etiqueta</label>
    <input type="text" class="form-control" id="ejemplo" placeholder="Placeholder">
    <small class="form-text">Texto de ayuda</small>
</div>

<!-- Con validación -->
<input type="email" class="form-control is-invalid" id="email">
```

### Alertas

```html
<!-- Success -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i> Mensaje de éxito
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Danger -->
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill"></i> Mensaje de error
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Warning -->
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle-fill"></i> Mensaje de advertencia
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Info -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-info-circle-fill"></i> Mensaje informativo
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

### Badges

```html
<span class="badge badge-primary">Primario</span>
<span class="badge badge-success">Éxito</span>
<span class="badge badge-danger">Peligro</span>
<span class="badge badge-warning">Advertencia</span>
<span class="badge badge-info">Información</span>
```

### Tablas

```html
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Columna 1</th>
                <th>Columna 2</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dato 1</td>
                <td>Dato 2</td>
                <td>
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### Modales

```html
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#miModal">
    Abrir Modal
</button>

<div class="modal fade" id="miModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Título del Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Contenido aquí
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
```

---

## ⚡ Clases de Utilidad

### Animaciones

```html
<!-- Fade In -->
<div class="fade-in">Contenido que aparece suavemente</div>

<!-- Slide In -->
<div class="slide-in">Contenido que se desliza</div>

<!-- Slide In Up -->
<div class="slide-in-up">Contenido que se desliza desde abajo</div>

<!-- Pulse -->
<div class="pulse">Contenido que pulsa</div>

<!-- Spin -->
<div class="spin">Contenido que gira</div>
```

### Texto

```html
<!-- Tamaños -->
<span class="text-xs">Extra pequeño</span>
<span class="text-sm">Pequeño</span>

<!-- Pesos -->
<span class="font-weight-bold">Negrita</span>

<!-- Colores -->
<span class="text-muted">Mutted</span>
<span class="text-danger">Peligro</span>
```

### Espaciado

```html
<!-- Margin y Padding -->
<div class="mb-4">Margen inferior grande</div>
<div class="mt-3">Margen superior medio</div>
<div class="p-3">Padding en todos lados</div>
```

### Otros

```html
<!-- Cursor -->
<div class="cursor-pointer">Clickeable</div>

<!-- Opacidad -->
<div class="opacity-50">50% opaco</div>
<div class="opacity-75">75% opaco</div>

<!-- Display -->
<div class="d-flex">Flexbox</div>
<div class="d-grid">Grid</div>
```

---

## 🎯 Transiciones Disponibles

```css
--transition-fast: 150ms ease-out     /* Para interacciones rápidas */
--transition-base: 200ms ease-out     /* Transiciones estándar */
--transition-slow: 300ms ease-out     /* Para animaciones lentas */
```

---

## 📱 Responsive Classes

```html
<!-- Mostrar solo en desktop -->
<div class="d-none d-md-block">Solo Desktop</div>

<!-- Mostrar solo en móvil -->
<div class="d-md-none">Solo Móvil</div>

<!-- Responsive grid -->
<div class="row">
    <div class="col-12 col-md-6 col-lg-3">Contenido</div>
</div>
```

---

## 🎨 Ejemplos de Implementación

### Dashboard Card

```html
<div class="row g-3 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 style="color: #0071e3; font-weight: 700; margin-bottom: 0.75rem;">Título</h6>
                    <div class="stat-value">$12,345</div>
                    <div class="stat-subtitle">Subtítulo descriptivo</div>
                </div>
                <div style="font-size: 2.8rem; color: #0071e3; opacity: 0.1;">
                    <i class="bi bi-icon-name"></i>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Alert Box

```html
<div class="alert alert-danger alert-dismissible fade show" style="border-left: 4px solid #ff3b30; background: rgba(255, 59, 48, 0.08);" role="alert">
    <div style="display: flex; align-items: flex-start; gap: 1rem;">
        <div style="font-size: 1.5rem; color: #ff3b30; flex-shrink: 0;">
            <i class="bi bi-exclamation-circle"></i>
        </div>
        <div>
            <h6 class="alert-heading" style="color: #ff3b30; margin-bottom: 0.5rem; font-weight: 700;">Título</h6>
            <p class="mb-0" style="color: #991b1b; font-size: 0.9rem;">Mensaje descriptivo</p>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

### Form Group

```html
<div class="form-group mb-4">
    <label for="inputNombre" class="form-label">Nombre Completo</label>
    <input type="text" class="form-control" id="inputNombre" placeholder="Ingrese nombre" required>
    <small class="form-text">Campo obligatorio para continuar</small>
</div>
```

---

## 🚀 Best Practices

### 1. Colores
- Usa variables CSS en lugar de valores hardcoded
- Mantén consistencia con la paleta de colores
- Usa `opacity` para variaciones sutiles

### 2. Transiciones
- Usa `--transition-base` para la mayoría de casos
- `--transition-fast` para micro-interacciones
- `--transition-slow` para animaciones entrantes

### 3. Animaciones
- Aplica `.fade-in` a contenido que carga dinámicamente
- Usa `.slide-in` para paneles laterales
- Aplica `.slide-in-up` a modales y alertas

### 4. Responsividad
- Mobile-first en el desarrollo
- Usa breakpoints: 576px, 768px, 992px, 1200px
- Prueba en dispositivos reales

### 5. Accesibilidad
- Mantén tamaño mínimo de fuente 16px en inputs
- Asegura buen contraste (WCAG AA)
- Usa `title` attributes en elementos interactivos
- Proporciona `aria-label` donde sea necesario

---

## 🔧 Personalización

### Cambiar Color Principal

1. Editar `/assets/css/style.css`
2. Encontrar `:root { --primary-color: #0071e3; }`
3. Cambiar valor hexadecimal
4. Actualizar variantes `--primary-light` y `--primary-dark`

### Agregar Nueva Animación

```css
@keyframes miAnimacion {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.mi-animacion {
    animation: miAnimacion var(--transition-slow);
}
```

### Crear Nuevo Componente

1. Basarse en clases existentes
2. Usar variables CSS
3. Agregar transiciones suaves
4. Probar en múltiples dispositivos

---

## 📞 Soporte

Para problemas o preguntas:
1. Revisar esta guía
2. Verificar ejemplos en vistas existentes
3. Consultar documentación de Bootstrap 5
4. Verificar console.log para errores JavaScript

---

**Última actualización:** 14 de febrero de 2026  
**Versión:** 2.0

