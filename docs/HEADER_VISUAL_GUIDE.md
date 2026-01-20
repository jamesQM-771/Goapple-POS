# 🎨 Guía Visual del Nuevo Header

## Vista Desktop (1920x1080)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  [🍎] GoApple [POS]  │    🔍 Buscar productos...    │ [+Nuevo] [🔔3] [👤]  │
└─────────────────────────────────────────────────────────────────────────────┘
    ↑                         ↑                            ↑      ↑     ↑
   Logo                   Búsqueda                     Acciones  Notif Usuario
```

### Componentes Desktop:
- **Logo**: Icono Apple en contenedor con fondo translúcido + texto "GoApple" + badge "POS"
- **Búsqueda**: Barra completa con dropdown de resultados categorizado
- **+ Nuevo**: Menú de acciones rápidas (Venta, Cliente, Producto, Proveedor)
- **🔔**: Notificaciones con badge (3) y dropdown con cards
- **👤**: Avatar + nombre + rol, con dropdown de perfil

---

## Vista Mobile (375x667)

```
┌───────────────────────────────────┐
│ [🍎] GoApple  [🔍] [👤] [☰]      │
└───────────────────────────────────┘
    ↑              ↑    ↑    ↑
   Logo         Search User Menu
```

### Componentes Mobile:
- **Logo**: Compacto, sin texto "GoApple" (solo en pantallas muy pequeñas)
- **🔍**: Abre modal fullscreen de búsqueda
- **👤**: Dropdown simplificado con perfil
- **☰**: Toggle para abrir sidebar

---

## 🎨 Paleta de Colores

### Navbar
```
Background: linear-gradient(135deg, #0071e3 0%, #0066cc 100%)
Shadow: 0 4px 20px rgba(0, 113, 227, 0.25)
Height: 70px (Desktop) | 60px (Mobile)
```

### Botones
```
Normal: rgba(255,255,255,0.15)
Hover: rgba(255,255,255,0.25)
Active: rgba(255,255,255,0.35)
Border-radius: 10px
Transition: 0.2s ease
```

### Dropdowns
```
Background: white
Border: none
Border-radius: 12px
Shadow: 0 8px 24px rgba(0,0,0,0.15)
Padding: 0.5rem
Animation: slideDown 0.2s ease
```

---

## 🔍 Barra de Búsqueda

### Estados:
1. **Normal**: Fondo rgba(255,255,255,0.9)
2. **Focus**: Shadow azul + border primary
3. **Con Resultados**: Dropdown visible debajo

### Resultados Agrupados:
```
┌─────────────────────────────────┐
│  PRODUCTOS                      │
│  [📱] iPhone 13 Pro 256GB      │
│  [📱] iPhone 14 128GB          │
│                                 │
│  CLIENTES                       │
│  [👤] Juan Pérez               │
│  [👤] María García             │
│                                 │
│  VENTAS                         │
│  [🛒] Venta #00123             │
└─────────────────────────────────┘
```

---

## ⚡ Acciones Rápidas

```
┌──────────────────────────────┐
│  ACCIONES RÁPIDAS            │
├──────────────────────────────┤
│  [🛒] Nueva Venta            │
│  [👤] Nuevo Cliente          │
│  [📱] Nuevo Producto         │
│  [🚚] Nuevo Proveedor        │
└──────────────────────────────┘
```

### Iconos y Colores:
- 🛒 Nueva Venta: `bi-cart-plus` (azul)
- 👤 Nuevo Cliente: `bi-person-plus` (verde)
- 📱 Nuevo Producto: `bi-phone` (cyan)
- 🚚 Nuevo Proveedor: `bi-truck` (naranja)

---

## 🔔 Notificaciones

```
┌─────────────────────────────────────┐
│  Notificaciones              [3]    │
├─────────────────────────────────────┤
│  [⚠️]  Créditos en mora             │
│       5 clientes con pagos          │
│       Hace 2 horas                  │
├─────────────────────────────────────┤
│  [📦]  Stock bajo                   │
│       3 productos requieren...      │
│       Hace 5 horas                  │
├─────────────────────────────────────┤
│  [✅]  Nueva venta registrada       │
│       iPhone 13 Pro vendido...      │
│       Hace 1 día                    │
├─────────────────────────────────────┤
│  Ver todas las notificaciones       │
└─────────────────────────────────────┘
```

### Tipos de Notificación:
- ⚠️ Alerta (rojo): Créditos en mora, problemas críticos
- 📦 Aviso (naranja): Stock bajo, mantenimiento
- ✅ Info (verde): Ventas, actualizaciones exitosas

---

## 👤 Menú de Usuario

### Desktop:
```
┌──────────────────────────────────┐
│  [A]  Admin Usuario              │
│       admin@goapple.com          │
│       [Admin]                    │
├──────────────────────────────────┤
│  [👤] Mi Perfil                  │
│  [⚙️] Configuración              │
├──────────────────────────────────┤
│  [🚪] Cerrar Sesión              │
└──────────────────────────────────┘
```

### Elementos:
- **Avatar Grande**: 48px, gradiente azul
- **Nombre Completo**: Bold, 0.95rem
- **Email**: Gris, 0.8rem, truncado
- **Badge de Rol**: Gradiente, 0.7rem
- **Links**: Con iconos coloridos
- **Cerrar Sesión**: Rojo, negrita

---

## 📐 Medidas y Espaciado

### Desktop:
- Navbar Height: `70px`
- Container Padding: `0 1.5rem`
- Logo Width: `40px`
- Logo Icon: `1.6rem`
- Search Max-Width: `500px`
- Button Padding: `0.65rem 1rem`
- Gap between items: `0.5rem` (8px)

### Mobile:
- Navbar Height: `60px`
- Container Padding: `0 1rem`
- Logo Width: `36px`
- Logo Icon: `1.4rem`
- Button Padding: `0.5rem 0.65rem`
- Gap between items: `0.5rem` (8px)

---

## 🎭 Efectos y Animaciones

### Logo Hover:
```css
transform: scale(1.03) translateY(-1px);
transition: 0.2s ease;
```

### Botones Hover:
```css
transform: translateY(-1px);
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
background: rgba(255,255,255,0.25);
```

### Dropdown Items Hover:
```css
background: var(--gray-100);
transform: translateX(4px);
transition: 0.2s ease;
```

### Badge Pulse:
```css
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
animation: pulse 2s infinite;
```

### Dropdown Appear:
```css
@keyframes slideDown {
    from { 
        opacity: 0; 
        transform: translateY(-10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}
```

---

## 🎯 Breakpoints Responsive

```css
/* Desktop */
@media (min-width: 1200px) {
    - Navbar: 70px
    - Search: visible (max-width 500px)
    - Actions: visible con texto
    - User: avatar + nombre + rol
}

/* Laptop */
@media (min-width: 992px) and (max-width: 1199px) {
    - Navbar: 70px
    - Search: visible
    - Actions: solo iconos
    - User: avatar + nombre
}

/* Tablet */
@media (min-width: 769px) and (max-width: 991px) {
    - Navbar: 60px
    - Search: hidden (usar modal)
    - Actions: iconos compactos
    - User: solo avatar
}

/* Mobile */
@media (max-width: 768px) {
    - Navbar: 60px
    - Search: modal fullscreen
    - Actions: solo iconos esenciales
    - User: dropdown simple
    - Sidebar: toggle visible
}
```

---

## 🔧 Clases CSS Principales

### Navbar:
```css
.navbar                    /* Contenedor principal */
.navbar-brand              /* Logo y nombre */
.navbar .btn               /* Todos los botones */
.navbar .dropdown-menu     /* Dropdowns */
.navbar .dropdown-item     /* Items de dropdown */
```

### Búsqueda:
```css
#globalSearch              /* Input desktop */
#mobileGlobalSearch        /* Input mobile */
#searchResults             /* Resultados desktop */
#mobileSearchResults       /* Resultados mobile */
.search-result-item        /* Item individual */
```

### Notificaciones:
```css
#notificacionesDropdown    /* Botón notificaciones */
#notif-count               /* Badge contador */
.notification-item         /* Item individual */
```

### Usuario:
```css
#userDropdown              /* Botón usuario desktop */
#mobileUserDropdown        /* Botón usuario mobile */
.user-avatar               /* Avatar circular */
```

---

## ✅ Checklist de Funcionalidad

### Desktop ✅
- [x] Logo con hover effect
- [x] Búsqueda en tiempo real
- [x] Resultados categorizados
- [x] Menú acciones rápidas
- [x] Notificaciones con badge
- [x] Perfil de usuario completo
- [x] Tooltips en botones
- [x] Animaciones suaves

### Mobile ✅
- [x] Logo responsive
- [x] Modal de búsqueda
- [x] Botones touch-friendly
- [x] Menú usuario simplificado
- [x] Toggle sidebar
- [x] Notificaciones adaptadas
- [x] Layout optimizado

### Accesibilidad ✅
- [x] Labels ARIA
- [x] Contraste WCAG AA
- [x] Touch targets > 44px
- [x] Navegación por teclado
- [x] Focus visible
- [x] Textos alternativos

---

## 🚀 Performance

### Optimizaciones:
- ⚡ Debounce en búsqueda (300ms)
- ⚡ CSS en cascada correcto
- ⚡ Animaciones GPU (transform)
- ⚡ Event delegation
- ⚡ Click outside handlers eficientes

### Métricas Objetivo:
- Tiempo de carga: < 100ms
- FPS animaciones: 60fps
- Tiempo respuesta búsqueda: < 500ms
- Tamaño CSS header: ~5KB
- Tamaño JS header: ~3KB

---

**Nota**: Todos los elementos son completamente funcionales y responsive.
El diseño sigue los principios de Apple Human Interface Guidelines.
