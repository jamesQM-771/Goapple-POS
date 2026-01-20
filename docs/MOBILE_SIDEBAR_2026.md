# Mobile Sidebar Profesional - 2026

## ✅ Cambios Implementados

### 1. **Header.php - Nuevo Hamburguesa para Móviles**
- ✅ Botón hamburguesa (`#mobileMenuToggle`) agregado al header
- ✅ Botón solo visible en dispositivos móviles (display: none en escritorio)
- ✅ Estilos profesionales con gradiente azul y efecto hover

### 2. **Sidebar Móvil HTML**
Ubicación: `/views/layouts/header.php`

Estructura completa:
```
<div id="mobileSidebar" class="mobile-sidebar">
  ├── Header (Menú + Botón cerrar)
  ├── Contenido (Navegación por secciones)
  │   ├── PRINCIPAL (Dashboard)
  │   ├── VENTAS (Historial, Nueva venta)
  │   ├── CLIENTES (Lista, Nuevo)
  │   ├── INVENTARIO (Productos, Nuevo)
  │   ├── CRÉDITOS (Créditos, En mora)
  │   └── REPORTES (Ventas, Ganancias)
  └── Footer (Configuración, Cerrar sesión)
```

### 3. **CSS - Mobile Sidebar (mobile.css)**

#### Características:
- **Ancho**: 280px (optimizado para móviles)
- **Fondo**: Gradiente azul `#0071e3` → `#0066cc` (match con header)
- **Animación**: Slide de izquierda (`transform: translateX(-100%)`)
- **Transición**: 0.3s cubic-bezier smooth
- **Z-index**: 1050 (por encima de todo)
- **Shadow**: Sombra elegante de 4px
- **Overflow**: Auto-scroll con scrollbar personalizado

#### Secciones:
1. **Header**
   - Título "Menú"
   - Botón cerrar con estilo profesional
   - Borde inferior sutil

2. **Contenido**
   - 6 secciones de navegación
   - Iconos Bootstrap Icons
   - Estilos hover con fondo y borde izquierdo
   - Links activos con estilo diferenciado

3. **Footer**
   - Configuración
   - Cerrar Sesión (en rojo)
   - Fondo oscuro para diferenciación

#### Colores y Efectos:
- **Color de texto**: `rgba(255, 255, 255, 0.9)` (blanco semi-transparente)
- **Hover**: `rgba(255, 255, 255, 0.1)` background + borde blanco
- **Active**: `rgba(255, 255, 255, 0.15)` background + borde blanco
- **Logout**: Rojo (`#ff4757`)

### 4. **Overlay (Dimmer)**
- Posición: Fixed, cubriendo toda la pantalla
- Color: `rgba(0, 0, 0, 0.5)` (gris oscuro semi-transparente)
- Animación: Fade in/out suave
- Cierre: Click en overlay cierra el sidebar

### 5. **JavaScript (main.js)**

#### Funcionalidad:
```javascript
// Abrir sidebar
document.getElementById('mobileMenuToggle').click()

// Cerrar sidebar
- Click en close button
- Click en overlay
- Click en un link de navegación
- Presionar ESC

// Prevenir scroll
document.body.style.overflow = 'hidden' (cuando abierto)
```

#### Features:
- ✅ Toggle abierto/cerrado
- ✅ Cierre automático al navegar
- ✅ Soporte ESC para cerrar
- ✅ Prevención de scroll cuando abierto
- ✅ Validación de elementos antes de usar

### 6. **Style.css - Botón Hamburguesa**

Estilos agregados:
```css
#mobileMenuToggle {
  transition: all 0.2s ease;
  hover: scale(1.05) + darker background
  active: scale(0.95)
}
```

---

## 📱 Responsividad

### Desktop (≥ 992px)
- Botón hamburguesa: **OCULTO** (`d-lg-none`)
- Sidebar: **NO SE MUESTRA**
- Header: Normal con buscar, notificaciones, acciones

### Tablet/Mobile (< 992px)
- Botón hamburguesa: **VISIBLE**
- Sidebar: Disponible con toggle
- Header: Simplificado (sin buscar, menos acciones visibles)

---

## 🎨 Colores y Gradientes

| Elemento | Color | Código |
|----------|-------|--------|
| Sidebar Background | Azul Gradiente | `#0071e3` → `#0066cc` |
| Texto Normal | Blanco 90% | `rgba(255, 255, 255, 0.9)` |
| Hover Background | Blanco 10% | `rgba(255, 255, 255, 0.1)` |
| Active Background | Blanco 15% | `rgba(255, 255, 255, 0.15)` |
| Overlay | Negro 50% | `rgba(0, 0, 0, 0.5)` |
| Logout Link | Rojo | `#ff4757` |

---

## 📱 Navegación Disponible

### Secciones:
1. **PRINCIPAL**: Dashboard
2. **VENTAS**: Historial, Nueva Venta
3. **CLIENTES**: Lista, Nuevo Cliente
4. **INVENTARIO**: Productos, Nuevo Producto
5. **CRÉDITOS**: Créditos, En Mora
6. **REPORTES**: Ventas, Ganancias
7. **USUARIO**: Configuración, Cerrar Sesión

---

## 🔧 Archivos Modificados

1. **`/views/layouts/header.php`**
   - Línea ~75: Agregado botón hamburguesa
   - Línea ~244: Agregado sidebar HTML completo
   - Línea ~330: Agregado overlay

2. **`/assets/css/mobile.css`**
   - Línea ~23-134: Nuevos estilos del sidebar móvil

3. **`/assets/css/style.css`**
   - Línea ~215-225: Estilos del botón hamburguesa

4. **`/assets/js/main.js`**
   - Línea ~292-372: JavaScript para toggle del sidebar

---

## ✨ Features Destacadas

- ✅ Diseño profesional y minimalista
- ✅ Animaciones suaves (cubic-bezier)
- ✅ Responsividad completa
- ✅ Accesibilidad (ARIA labels, ESC key)
- ✅ Performance optimizado (no heavy scripts)
- ✅ Cierre automático al navegar
- ✅ Scrollbar personalizado
- ✅ Overlay inteligente

---

## 🚀 Pruebas Recomendadas

1. **Abierto/Cerrado**
   - Click hamburguesa → abre sidebar
   - Click botón cerrar → cierra sidebar
   - Click overlay → cierra sidebar
   - Presionar ESC → cierra sidebar

2. **Navegación**
   - Click en link → cierra automáticamente
   - Cada link va a su destino

3. **Responsividad**
   - Desktop: Sin hamburguesa
   - Tablet: Hamburguesa visible
   - Mobile: Hamburguesa visible

4. **Animaciones**
   - Slide suave del sidebar
   - Fade del overlay
   - Hover effects en links

---

## 💡 Mejoras Futuras

- [ ] Iconos con badge para notificaciones en items
- [ ] Collapse/expand para subsecciones
- [ ] Búsqueda dentro del sidebar
- [ ] Shortcuts de teclado para favoritos
- [ ] Dark mode toggle

---

**Última actualización**: 15 de febrero, 2026
**Estado**: ✅ COMPLETO Y FUNCIONAL
