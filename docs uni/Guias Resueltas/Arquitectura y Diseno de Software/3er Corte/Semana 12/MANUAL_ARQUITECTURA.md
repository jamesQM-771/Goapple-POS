# Manual de Arquitectura — GoApple POS

**Guía Práctica N° 12 - Documentación Técnica Final y Consolidación del MVP**
**Versión:** 1.0.0
**Fecha:** 26/05/2026

---

## 1. Vista de Despliegue

### Entorno de Desarrollo

| Componente | Especificación |
|------------|---------------|
| **Servidor Web** | Apache 2.4 (Laragon) |
| **PHP** | 8.x |
| **Base de Datos** | MySQL 8.x |
| **Sistema Operativo** | Windows 10/11 |
| **URL Base** | `http://localhost/goapple` |

### Arquitectura de Despliegue

```
┌─────────────────────────────────────────────────────┐
│                   Laragon                           │
│  ┌─────────────────┐    ┌──────────────────────┐   │
│  │    Apache        │    │      MySQL 8.x       │   │
│  │  (PHP 8.x)       │    │   goapple_pos DB     │   │
│  │                  │    │                      │   │
│  │  http://localhost│    │  17 tablas, views,   │   │
│  │  /goapple/       │    │  triggers, proced.   │   │
│  └─────────────────┘    └──────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

### Stack Tecnológico

```
Frontend (HTML/CSS/JS) → Backend (PHP MVC) → MySQL
        ↓                      ↓
   Bootstrap 5           Patrón Singleton DB
   jQuery                PDO Prepared Statements
   XSLT (Semana 8)       SOAP (Semana 9)
```

---

## 2. Diccionario de Datos

### Tabla: `usuarios`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT PK AUTO_INCREMENT | Identificador único |
| nombre | VARCHAR(100) | Nombre completo del usuario |
| email | VARCHAR(100) UNIQUE | Correo electrónico (login) |
| password | VARCHAR(255) | Hash bcrypt de la contraseña |
| rol | ENUM('administrador','vendedor') | Rol en el sistema |
| telefono | VARCHAR(20) | Número de contacto |
| estado | ENUM('activo','inactivo') | Estado de la cuenta |
| fecha_creacion | TIMESTAMP | Fecha de registro |
| ultimo_acceso | TIMESTAMP NULL | Último inicio de sesión |

### Tabla: `clientes`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT PK AUTO_INCREMENT | Identificador único |
| nombre | VARCHAR(100) | Nombre completo |
| cedula | VARCHAR(20) UNIQUE | Número de cédula |
| telefono | VARCHAR(20) | Teléfono de contacto |
| email | VARCHAR(100) | Correo electrónico |
| direccion | TEXT | Dirección de residencia |
| ciudad | VARCHAR(100) | Ciudad |
| estado | ENUM('activo','moroso','bloqueado') | Estado del cliente |
| limite_credito | DECIMAL(15,2) | Límite máximo de crédito |
| credito_disponible | DECIMAL(15,2) | Crédito disponible actual |
| total_compras | DECIMAL(15,2) | Total acumulado de compras |

### Tabla: `iphones`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT PK AUTO_INCREMENT | Identificador único |
| modelo | VARCHAR(100) | Modelo del iPhone (ej. iPhone 16 Pro) |
| capacidad | VARCHAR(20) | Almacenamiento (ej. 256GB) |
| color | VARCHAR(50) | Color del dispositivo |
| condicion | ENUM('nuevo','usado') | Estado físico |
| estado_bateria | INT | Porcentaje de salud de batería |
| imei | VARCHAR(20) UNIQUE | IMEI del dispositivo |
| proveedor_id | INT FK | Referencia al proveedor |
| precio_compra | DECIMAL(15,2) | Precio de adquisición |
| precio_venta | DECIMAL(15,2) | Precio de venta al público |
| estado | ENUM('disponible','vendido','en_credito','apartado') | Estado en inventario |

### Tabla: `ventas`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT PK AUTO_INCREMENT | Identificador único |
| numero_venta | VARCHAR(20) UNIQUE | Número de factura (Ej: VEN-000001) |
| cliente_id | INT FK | Cliente que compra |
| vendedor_id | INT FK | Vendedor que atendió |
| tipo_venta | ENUM('contado','credito') | Modalidad de pago |
| subtotal | DECIMAL(15,2) | Subtotal sin impuestos |
| impuesto | DECIMAL(15,2) | IVA u otros impuestos |
| descuento | DECIMAL(15,2) | Descuento aplicado |
| total | DECIMAL(15,2) | Total a pagar |
| metodo_pago | VARCHAR(50) | Efectivo, tarjeta, etc. |

### Tabla: `creditos`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT PK AUTO_INCREMENT | Identificador único |
| numero_credito | VARCHAR(20) UNIQUE | Número de crédito (CRE-000001) |
| venta_id | INT FK | Venta asociada |
| cliente_id | INT FK | Cliente asociado |
| monto_total | DECIMAL(15,2) | Monto total del crédito |
| interes | DECIMAL(15,2) | Interés total |
| saldo_pendiente | DECIMAL(15,2) | Saldo por pagar |
| numero_cuotas | INT | Total de cuotas |
| cuotas_pagadas | INT | Cuotas pagadas |
| estado | ENUM('activo','pagado','mora','cancelado') | Estado actual |

### Tabla: `apartados`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT PK AUTO_INCREMENT | Identificador único |
| numero_apartado | VARCHAR(20) UNIQUE | Número de apartado |
| cliente_id | INT FK | Cliente que aparta |
| iphone_id | INT FK | iPhone apartado |
| abono_inicial | DECIMAL(15,2) | Abono inicial |
| saldo_pendiente | DECIMAL(15,2) | Saldo por pagar |
| fecha_limite | DATE | Fecha límite del apartado |
| estado | ENUM('activo','completado','cancelado','expirado') | Estado |

*(Ver archivo completo `sql/database.sql` para las 17 tablas, triggers, vistas y procedimientos almacenados)*

---

## 3. Estructura de Directorios

```
goapple/
│
├── config/                     # Configuración del sistema
│   ├── config.php             # Constantes, sesión, autoload
│   ├── database.php           # Clase Database (Singleton PDO)
│   └── env.php                # Variables de entorno (DB credenciales)
│
├── controllers/                # Controladores (lógica de negocio)
│   ├── AuthController.php     # Autenticación de usuarios
│   ├── ClienteController.php  # CRUD de clientes
│   ├── VentaController.php    # Procesamiento de ventas
│   └── ...
│
├── models/                     # Modelos (acceso a datos)
│   ├── Usuario.php            # Operaciones sobre usuarios
│   ├── Cliente.php            # Operaciones sobre clientes
│   ├── iPhone.php             # Operaciones sobre inventario
│   ├── Venta.php              # Operaciones sobre ventas
│   ├── Credito.php            # Gestión de créditos
│   ├── Apartado.php           # Gestión de apartados
│   ├── Proveedor.php          # Operaciones sobre proveedores
│   ├── Devolucion.php         # Gestión de devoluciones
│   ├── Comision.php           # Cálculo de comisiones
│   ├── Configuracion.php      # Configuración dinámica
│   ├── Foto.php               # Gestión de fotos
│   └── Notificacion.php       # Sistema de notificaciones
│
├── views/                      # Vistas (presentación HTML)
│   ├── layouts/               # Plantillas base
│   │   ├── header.php         # Cabecera HTML + menú
│   │   └── footer.php         # Pie de página + scripts
│   │
│   ├── auth/                  # Vistas de autenticación
│   ├── dashboard/             # Panel principal
│   ├── clientes/              # Gestión de clientes
│   ├── ventas/                # Gestión de ventas
│   ├── creditos/              # Gestión de créditos
│   ├── apartados/             # Gestión de apartados
│   ├── inventario/            # Gestión de iPhones
│   ├── proveedores/           # Gestión de proveedores
│   ├── devoluciones/          # Gestión de devoluciones
│   ├── comisiones/            # Gestión de comisiones
│   ├── usuarios/              # Gestión de usuarios
│   └── reportes/              # Reportes del sistema
│
├── assets/                     # Recursos estáticos
│   ├── css/                   # Hojas de estilo
│   ├── js/                    # Scripts JavaScript
│   └── img/                   # Imágenes
│
├── uploads/                    # Archivos subidos
│   ├── fotos/                 # Fotos de productos
│   ├── compras/               # Comprobantes de compra
│   └── ventas/                # Comprobantes de venta
│
├── sql/                        # Archivos de base de datos
│   └── database.sql           # Esquema completo con datos de prueba
│
├── tools/                      # Utilidades
│   └── exportar_ventas.php    # Exportación de datos
│
├── docs/                       # Documentación del proyecto
│
├── docs uni/                   # Guías resueltas (actividades académicas)
│   └── Guias Resueltas/
│       └── Arquitectura y Diseno de Software/
│           ├── 1er Corte/
│           ├── 2do Corte/
│           └── 3er Corte/
│               ├── Semana 8/  # XML/XSD/XSLT - Integración con DB real
│               ├── Semana 9/  # SOAP/WSDL - Integración con DB real
│               ├── Semana 10/ # Service Registry - Consumo SOAP real
│               ├── Semana 11/ # Refactorización - Documentación
│               └── Semana 12/ # Documentación técnica final
│
└── login.php                   # Punto de entrada al sistema
```

### Responsabilidad de Capas (Patrón MVC)

| Capa | Responsabilidad | Prohibiciones |
|------|----------------|---------------|
| **Modelo** | Acceso a BD, lógica de negocio, reglas de validación | No debe generar HTML |
| **Vista** | Presentación HTML, formularios, tablas | No debe contener SQL ni lógica de negocio |
| **Controlador** | Orquestación, validación de entrada, redirección | No debe contener SQL directo |
| **Config** | Conexión BD, constantes, autoload | No debe contener lógica de negocio |
