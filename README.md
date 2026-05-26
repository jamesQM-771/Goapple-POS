# GoApple POS

Sistema de Punto de Venta para tienda de iPhones con gestión de inventario, ventas al contado y crédito, apartados, comisiones y más.

## Requisitos Previos

- [Laragon](https://laragon.org/) (Apache + PHP 8.x + MySQL 8.x)
- PHP 8.0 o superior (con extensiones: `pdo_mysql`, `curl`, `soap`, `xsl`, `dom`, `mbstring`)
- MySQL 8.0 o superior
- Git (opcional)

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/goapple.git
```

O copia la carpeta `goapple/` dentro de `C:\laragon\www\`.

### 2. Importar la base de datos

1. Abre Laragon → MySQL → MySQL Command Line
2. Ejecuta:

```sql
mysql -u root < C:\laragon\www\goapple\sql\database.sql
```

O importa `sql/database.sql` desde phpMyAdmin.

### 3. Configurar conexión

Edita `config/env.php` si usas credenciales diferentes:

```php
return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'goapple_pos',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    // ...
];
```

### 4. Iniciar Laragon

- Inicia Laragon → `Start All`
- Visita: [http://localhost/goapple](http://localhost/goapple)

## Credenciales de Prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Administrador** | admin@goapple.com | admin123 |
| **Vendedor** | vendedor@goapple.com | admin123 |

## Acceso Rápido a Módulos

| Módulo | URL |
|--------|-----|
| Login | `http://localhost/goapple/login.php` |
| Dashboard | `http://localhost/goapple/views/dashboard/index.php` |
| Inventario (iPhones) | `http://localhost/goapple/views/inventario/lista.php` |
| Ventas | `http://localhost/goapple/views/ventas/nueva.php` |
| Clientes | `http://localhost/goapple/views/clientes/lista.php` |
| Créditos | `http://localhost/goapple/views/creditos/lista.php` |
| Apartados | `http://localhost/goapple/views/apartados/lista.php` |

## Guías Resueltas (Actividades Académicas)

Las prácticas de laboratorio resueltas están en `docs uni/Guias Resueltas/`.  
Todas las semanas están integradas con la base de datos real `goapple_pos`.

Existen dos ramas de asignaturas: **Arquitectura y Diseño de Software** (base URL: `/Arquitectura%20y%20Diseno%20de%20Software/`) y **Arquitectura Cliente-Servidor** (base URL: `/Arquitectura%20Cliente-Servidor/`).

### 1er Corte

| Semana | Tecnología | URL de prueba |
|--------|-----------|---------------|
| 2 | Capa de Datos + Estadísticas | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/1er%20Corte/Semana%202` |
| 3 | Consumo de API SOAP externa | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/1er%20Corte/Semana%203` |
| 4 | Capa de Validación | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/1er%20Corte/Semana%204` |

### 2do Corte

| Semana | Tecnología | URL de prueba |
|--------|-----------|---------------|
| 5 | Conexión Singleton a BD real | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/2do%20Corte/Semana%205` |
| 6 | DAO + Inserción real en iphones | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/2do%20Corte/Semana%206` |
| 7 | Middleware DCOM → SOAP real | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/2do%20Corte/Semana%207/Middleware_DCOM.php` |

### 3er Corte

| Semana | Tecnología | URL de prueba |
|--------|-----------|---------------|
| 8 | XML/XSD/XSLT | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%208/cliente.php` |
| 9 | SOAP/WSDL | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%209/soap_client.php` |
| 10 | Service Registry | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%2010/consumidor_virtualizado.php` |
| 11 | Refactorización | Documentación en carpeta Semana 11 |
| 12 | Documentación Final | Documentación en carpeta Semana 12 |

---

### Arquitectura Cliente-Servidor (misma BD, URLs alternas)

#### 1er Corte

| Semana | Tema | URL |
|--------|------|-----|
| 1 | Procesos de Negocio Remotos | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/1er%20Corte/Semana%201/Guia1.php` |
| 2 | Topología de Red | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/1er%20Corte/Semana%202/Guia2_Topologia.php` |
| 3 | Handshake TCP/IP | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/1er%20Corte/Semana%203/client_handshake.php` |
| 4 | I/O de Payload | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/1er%20Corte/Semana%204/client_io.php` |

#### 2do Corte

| Semana | Tema | URL |
|--------|------|-----|
| 5 | Serialización de Objetos | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/2do%20Corte/Semana%205/Serializacion_Objetos.php` |
| 6 | Sistema de Nombrado (Registry) | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/2do%20Corte/Semana%206/Registry_System.php` |
| 7 | Middleware y DCOM | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/2do%20Corte/Semana%207/Middleware_DCOM.php` |

#### 3er Corte

| Semana | Tecnología | URL |
|--------|-----------|-----|
| 8 | XML/XSD/XSLT | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%208/cliente.php` |
| 9 | SOAP/WSDL | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_client.php` |
| 10 | Service Registry | `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%2010/consumidor_virtualizado.php` |

## Estructura del Proyecto

```
goapple/
├── config/          # Configuración (DB, constantes, autoload)
├── controllers/     # Controladores MVC
├── models/          # Modelos con acceso a datos (PDO)
├── views/           # Vistas HTML (layouts, módulos)
├── assets/          # CSS, JS, imágenes
├── uploads/         # Archivos subidos (fotos, comprobantes)
├── sql/             # Esquema de base de datos
├── tools/           # Utilidades (exportación, etc.)
└── docs uni/        # Guías académicas resueltas
```

## Stack Tecnológico

- **Backend:** PHP 8.x con patrón MVC y autoload PSR-4
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Base de Datos:** MySQL 8.x con PDO Prepared Statements
- **Servicios Web:** XML/XSD/XSLT (Semana 8), SOAP/WSDL (Semana 9)
- **Servidor:** Apache 2.4 (Laragon)
