# 📱 GOAPPLE POS - Sistema de Punto de Venta para Tienda de iPhones

Sistema completo de punto de venta (POS) desarrollado en PHP para tiendas de iPhones en Colombia. Incluye gestión de inventario, ventas de contado y crédito con intereses, clientes, proveedores y reportes financieros.

## 🚀 Características Principales

### 📦 Gestión de Inventario
- Registro completo de iPhones con modelo, capacidad, color, IMEI único
- Control de estado de batería y condición (nuevo/usado)
- Asociación con proveedores
- Estados: Disponible, Vendido, En crédito, Apartado
- Alertas de stock bajo

### 💰 Sistema de Ventas
- Ventas de contado
- Ventas a crédito con cálculo automático de intereses
- Múltiples formas de pago
- Descuentos
- Actualización automática de inventario
- Numeración consecutiva de ventas

### 💳 Créditos con Intereses
- Cálculo automático con interés compuesto
- Configuración de tasa de interés mensual
- Cuota inicial configurable
- Plan de cuotas personalizable
- Registro de abonos/pagos
- Control de moras y penalizaciones
- Alertas de próximos vencimientos
- Bloqueo automático de clientes morosos

### 👥 Gestión de Clientes
- Registro completo de datos
- Historial de compras y créditos
- Estados: Activo, Moroso, Bloqueado
- Límite de crédito personalizado
- Seguimiento de deudas

### 🏢 Gestión de Proveedores
- Registro con NIT/Cédula único
- Información de contacto completa
- Historial de productos suministrados
- Estadísticas por proveedor

### 📊 Reportes y Estadísticas
- Dashboard con métricas en tiempo real
- Reportes de ventas por período
- Análisis de créditos activos y en mora
- Cálculo de intereses generados
- Ganancias y flujo de caja
- Ranking de vendedores
- Productos más vendidos

### 🧾 Generación de PDFs
- Recibos de venta profesionales
- Recibos de abonos a crédito
- Logo y datos de la empresa
- Numeración consecutiva

### 🔐 Seguridad y Control
- Sistema de autenticación seguro con password hash
- Roles: Administrador y Vendedor
- Control de permisos por rol
- Sesiones con timeout
- Prepared statements para prevenir SQL injection
- Validación de datos en cliente y servidor

## 💻 Tecnologías Utilizadas

- **Backend:** PHP 8+
- **Base de Datos:** MySQL con PDO
- **Frontend:** HTML5, CSS3, Bootstrap 5
- **JavaScript:** jQuery, DataTables, Select2, Chart.js, SweetAlert2
- **Arquitectura:** MVC (Modelo-Vista-Controlador)
- **Generación PDF:** Compatible con DomPDF/TCPDF

## 📋 Requisitos del Sistema

- PHP 8.0 o superior
- MySQL 5.7 o superior / MariaDB 10.3 o superior
- Apache 2.4 o superior con mod_rewrite habilitado
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - mbstring
  - json
  - session

## 📥 Instalación

Ver archivo [INSTALL.md](INSTALL.md) para instrucciones detalladas de instalación.

### Instalación Rápida

1. **Copiar archivos al servidor**
   ```bash
   # Copiar el proyecto a htdocs de XAMPP
   cp -r GOAPPLE2 /Applications/XAMPP/xamppfiles/htdocs/
   ```

2. **Crear la base de datos**
   ```bash
   # Acceder a MySQL
   mysql -u root -p
   
   # Ejecutar el script SQL
   source /ruta/a/database.sql
   ```

3. **Configurar la conexión**
   - Editar `config/database.php`
   - Configurar credenciales de MySQL

4. **Acceder al sistema**
   ```
   URL: http://localhost/GOAPPLE2
   Usuario: admin@goapple.com
   Contraseña: admin123
   ```

## 📁 Estructura del Proyecto

```
GOAPPLE2/
├── config/              # Archivos de configuración
│   ├── database.php     # Conexión a BD
│   └── config.php       # Configuraciones generales
├── models/              # Modelos de datos
│   ├── Usuario.php
│   ├── Cliente.php
│   ├── Proveedor.php
│   ├── iPhone.php
│   ├── Venta.php
│   └── Credito.php
├── controllers/         # Controladores (lógica de negocio)
├── views/              # Vistas (interfaz de usuario)
│   ├── layouts/        # Plantillas header, footer, sidebar
│   ├── dashboard.php   # Panel principal
│   ├── ventas/         # Módulo de ventas
│   ├── creditos/       # Módulo de créditos
│   ├── inventario/     # Módulo de inventario
│   ├── clientes/       # Módulo de clientes
│   ├── proveedores/    # Módulo de proveedores
│   └── reportes/       # Módulo de reportes
├── assets/             # Recursos estáticos
│   ├── css/           # Estilos personalizados
│   ├── js/            # Scripts JavaScript
│   └── img/           # Imágenes y logo
├── uploads/            # Archivos subidos
├── database.sql        # Script de base de datos
├── index.php          # Punto de entrada
├── .htaccess          # Configuración Apache
└── README.md          # Este archivo
```

## 👤 Usuario por Defecto

Al instalar el sistema, se crea automáticamente un usuario administrador:

- **Email:** admin@goapple.com
- **Contraseña:** admin123
- **Rol:** Administrador

**⚠️ IMPORTANTE:** Cambiar la contraseña después del primer acceso.

## 🔧 Configuración

### Configuración de Empresa

Editar la tabla `configuracion` en la base de datos o usar el panel de administración:

- Nombre de la empresa
- NIT
- Teléfono
- Email
- Dirección
- Tasa de interés por defecto
- Días de tolerancia para mora
- Penalización por mora

### Configuración de Créditos

Configurar en `config/config.php`:

```php
define('TASA_INTERES_DEFAULT', 3.5);  // % mensual
define('DIAS_MORA_TOLERANCIA', 5);     // días
define('PENALIZACION_MORA', 5);        // %
```

## 📈 Módulos del Sistema

### 1. Dashboard
- Resumen de ventas del mes
- Estadísticas de créditos
- Inventario disponible
- Alertas importantes
- Accesos rápidos

### 2. Ventas
- Nueva venta (contado/crédito)
- Historial de ventas
- Detalles de venta
- Cancelación de ventas

### 3. Créditos
- Lista de créditos activos
- Créditos en mora
- Registro de pagos/abonos
- Generación de recibos
- Historial de pagos

### 4. Inventario
- Lista de iPhones
- Agregar nuevo iPhone
- Editar información
- Filtros avanzados
- Alertas de stock

### 5. Clientes
- Registro de clientes
- Historial de compras
- Gestión de créditos
- Estados y límites

### 6. Proveedores
- Gestión de proveedores
- Productos por proveedor
- Estadísticas

### 7. Reportes
- Ventas por período
- Análisis de créditos
- Ganancias e intereses
- Exportación a Excel/PDF

### 8. Administración
- Gestión de usuarios
- Configuración del sistema
- Respaldos (backup)

## 🛡️ Seguridad

- Contraseñas hasheadas con `password_hash()`
- Prepared statements para todas las consultas
- Validación de datos en cliente y servidor
- Protección CSRF
- Control de sesiones
- Timeout de inactividad
- Sanitización de inputs
- Protección contra XSS
- Headers de seguridad

## 🔄 Respaldo y Mantenimiento

### Respaldo de Base de Datos

```bash
mysqldump -u root -p goapple_pos > backup_$(date +%Y%m%d).sql
```

### Tareas de Mantenimiento

- Verificar créditos en mora diariamente
- Respaldar base de datos semanalmente
- Revisar logs de errores
- Actualizar contraseñas periódicamente

## 📞 Soporte y Contacto

Para soporte técnico o consultas:

- **Email:** soporte@goapple.com
- **Teléfono:** +57 300 123 4567

## 📄 Licencia

Este sistema es propiedad de GOAPPLE. Todos los derechos reservados.

## 🎯 Roadmap Futuro

- [ ] App móvil para vendedores
- [ ] Integración con pasarelas de pago
- [ ] Sistema de facturación electrónica
- [ ] Integración con WhatsApp para recordatorios
- [ ] API REST para integraciones
- [ ] Multi-tienda
- [ ] Modo offline

---

**Versión:** 1.0.0  
**Última actualización:** Febrero 2026  
**Desarrollado con ❤️ para tiendas de iPhones en Colombia**
