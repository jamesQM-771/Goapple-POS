# Sistema de Navegación Profesional 2026
## Sidebar Desktop + Offcanvas Mobile

### ✅ ESTADO: COMPLETADO Y FUNCIONAL

---

## 📐 Arquitectura del Navegación

### **DESKTOP (≥ 992px)**
- ✅ Sidebar fijo en la izquierda (260px ancho)
- ✅ Visible permanentemente
- ✅ Con animaciones hover profesionales
- ✅ Integrado con el header (70px top)

### **MOBILE & TABLET (< 992px)**
- ✅ Offcanvas (menú deslizable)
- ✅ Botón hamburguesa en header
- ✅ Se abre desde la izquierda
- ✅ Cierra automáticamente al navegar

---

## 🎨 DISEÑO PROFESIONAL

### **Desktop Sidebar**
```
┌─────────────────┐
│ SIDEBAR (260px) │  
├─────────────────┤
│ GoApple Logo    │  Header (1.5rem padding)
├─────────────────┤
│ PRINCIPAL       │  Secciones con títulos
│  - Dashboard    │  Links con iconos
├─────────────────┤
│ VENTAS          │  Colores: Azul #0071e3
│  - Historial    │  Hover: Fondo azul claro
│  - Nueva Venta  │  Borde izquierdo animado
├─────────────────┤
│ [más secciones] │
├─────────────────┤
│ USUARIO         │  Footer con estilos
│  - Config       │  especiales
│  - Logout (red) │
└─────────────────┘
```

### **Mobile Offcanvas**
- Ancho: 280px
- Fondo: Gris claro (#f8f9fa)
- Header: Gradiente azul (match con header)
- Animación: Slide suave desde izquierda
- Backdrop: Oscuro semi-transparente

---

## 📱 CARACTERÍSTICAS IMPLEMENTADAS

### **1. Sidebar Desktop**
✅ Navbar-brand con logo
✅ 6 secciones de navegación
✅ Iconos Bootstrap Icons
✅ Hover effects profesionales
✅ Borde izquierdo animado
✅ Scrollbar personalizado
✅ Footer con configuración y logout

### **2. Offcanvas Mobile**
✅ Bootstrap nativo (offcanvas-start)
✅ Cierres inteligentes:
   - Click en link → cierra automático
   - Click en backdrop → cierra
   - ESC key → cierra
✅ Data attributes para cierre
✅ Estilos responsive

### **3. Header Responsive**
✅ Botón hamburguesa (solo móvil)
✅ Logo y nombre adaptables
✅ Barra de búsqueda (desktop+ ≥ md)
✅ Acciones rápidas (desktop ≥ lg)
✅ Notificaciones (desktop ≥ lg)
✅ Menú usuario (desktop ≥ lg)

### **4. Layout Principal**
✅ Main-wrapper con flex
✅ Content area responsiva
✅ Margin-left en desktop (260px)
✅ Full-width en móvil

---

## 📁 ARCHIVOS MODIFICADOS

### 1. **views/layouts/header.php**
- Nuevo: `<aside class="sidebar d-none d-lg-block">` (línea ~246)
- Nuevo: `<div class="offcanvas offcanvas-start" id="mobileMenu">` (línea ~348)
- Actualizado: Botón hamburguesa (línea ~70)
- Actualizado: Estructura layout con main-wrapper

### 2. **assets/css/style.css**
- Línea ~216-300: Estilos completosdel sidebar:
  - `.sidebar` - Posición y estructura
  - `.sidebar-wrapper` - Flexbox
  - `.nav-group` y `.nav-item` - Items de navegación
  - `.sidebar-footer` - Footer especial
  - `.main-wrapper` - Container flex
  - Media queries para desktop

### 3. **assets/css/mobile.css**
- Línea ~24-110: Estilos del offcanvas
- Línea ~115-180: Media queries responsive
  - 992px+ (desktop)
  - < 992px (tablet/mobile)
  - < 576px (mobile pequeño)

### 4. **assets/js/main.js**
- Sin cambios (Bootstrap maneja offcanvas nativamente)

---

## 🎯 SECCIONES DE NAVEGACIÓN

```
PRINCIPAL
 └─ Dashboard

VENTAS
 ├─ Historial de Ventas
 └─ Nueva Venta

CLIENTES
 ├─ Lista de Clientes
 └─ Nuevo Cliente

INVENTARIO
 ├─ Productos
 └─ Nuevo Producto

CRÉDITOS
 ├─ Créditos
 └─ En Mora

REPORTES
 ├─ Ventas
 └─ Ganancias

USUARIO
 ├─ Configuración
 └─ Cerrar Sesión (rojo)
```

---

## 🎨 PALETA DE COLORES

| Elemento | Color | Código |
|----------|-------|--------|
| Sidebar Gradient | Blanco → Gris claro | #ffffff → #f8f9fa |
| Sidebar Border | Gris claro | #e5e7eb |
| Link Normal | Gris oscuro | #374151 |
| Link Icono | Azul | #0071e3 |
| Link Hover BG | Azul muy claro | rgba(0, 113, 227, 0.08) |
| Link Hover Border | Azul | #0071e3 |
| Logout Normal | Rojo | #ef4444 |
| Offcanvas BG | Gris claro | #f8f9fa |
| Header Gradient | Azul | #0071e3 → #0066cc |

---

## 📐 BREAKPOINTS UTILIZADOS

```
Mobile:        < 576px
Tablet:        576px - 991px
Desktop:       ≥ 992px

CSS Media Queries:
- @media (max-width: 576px)
- @media (max-width: 991px)
- @media (min-width: 992px)
- @media (min-width: 769px)
```

---

## ✨ EFECTOS Y TRANSICIONES

### **Sidebar Desktop**
```css
transition: all 0.2s ease;
- Hover: +padding-left, color change, background fade
- Border-left: 3px transparent → solid color
- Smooth scrollbar visible
```

### **Offcanvas Mobile**
```css
- Slide-in from left (Bootstrap default)
- Backdrop fade in/out
- ESC key support
- Data-dismiss on links
```

---

## 🧪 PRUEBAS REALIZADAS

✅ **Desktop (≥ 992px)**
- Sidebar visible permanentemente
- Hover effects funcionando
- Todos los links navegables
- Layout con espacio para sidebar

✅ **Tablet (768px - 991px)**
- Sidebar oculto
- Hamburguesa visible
- Offcanvas funcional
- Layout full-width

✅ **Mobile (< 768px)**
- Hamburguesa prominente
- Offcanvas deslizable
- Cierre automático
- Header simplificado

✅ **Interacciones**
- Click hamburguesa → abre offcanvas
- Click link → cierra offcanvas
- Click backdrop → cierra offcanvas
- ESC key → cierra offcanvas

---

## 🔧 USO EN TEMPLATES

```php
<!-- El header.php incluye:
  1. Navbar con header (sticky-top)
  2. Desktop Sidebar (d-none d-lg-block)
  3. Mobile Offcanvas (offcanvas offcanvas-start)
  4. Main wrapper con content
  
  Se auto-adapta según viewport
-->
```

---

## 📝 NOTAS TÉCNICAS

- **Framework**: Bootstrap 5 (Offcanvas nativo)
- **Responsive**: Mobile-first approach
- **Accesibilidad**: ARIA labels completos
- **Performance**: Sin JavaScript custom
- **Compatibilidad**: Todos los navegadores modernos

---

**Última actualización**: 15 de febrero, 2026  
**Estado**: ✅ PRODUCCIÓN LISTA
