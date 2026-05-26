# Manual de Interoperabilidad — API y Web Services

**Guía Práctica N° 12 - Documentación Técnica Final y Consolidación del MVP**
**Sistema:** GoApple POS

---

## 1. Protocolo XML (Semana 8)

### Endpoint
```
POST http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%208/servidor.php
```

### Cabeceras
```
Content-Type: application/xml
```

### Estructura del Mensaje (XSD)

```xml
<mensaje>
  <mensajes_control>
    <id_transaccion>UNIQUE_ID</id_transaccion>
    <timestamp>2026-05-26T10:00:00-05:00</timestamp>
    <emisor>Cliente-PHP</emisor>
  </mensajes_control>
  <request>
    <operacion>consultar_usuario</operacion>
    <datos>
      <item>
        <clave>usuario_id</clave>
        <valor>1</valor>
      </item>
    </datos>
  </request>
</mensaje>
```

### Operaciones Disponibles

#### `consultar_usuario`
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| usuario_id | string | ID o email del usuario |

**Respuesta Exitosa:**
```xml
<response>
  <operacion>consultar_usuario</operacion>
  <estado>EXITO</estado>
  <datos>
    <item><clave>id</clave><valor>1</valor></item>
    <item><clave>nombre</clave><valor>Admin GoApple</valor></item>
    <item><clave>email</clave><valor>admin@goapple.com</valor></item>
    <item><clave>rol</clave><valor>administrador</valor></item>
    <item><clave>telefono</clave><valor>3001234567</valor></item>
    <item><clave>estado</clave><valor>activo</valor></item>
  </datos>
</response>
```

#### `consultar_producto`
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| codigo | string | ID o IMEI del iPhone |

#### `listar_productos`
(Sin parámetros) — Retorna los últimos 50 productos.

#### `consultar_cliente`
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| cedula | string | ID o cédula del cliente |

### Errores
```xml
<error>
  <estado>FALLO</estado>
  <codigo>ERR-05</codigo>
  <mensaje_error>Usuario no encontrado.</mensaje_error>
</error>
```

| Código | Descripción |
|--------|-------------|
| ERR-01 | Cuerpo de la petición vacío |
| ERR-02 | XML malformado |
| ERR-03 | XML no cumple con el esquema XSD |
| ERR-04 | No es una solicitud válida |
| ERR-05 | Recurso no encontrado |
| ERR-06 | Operación no soportada |

---

## 2. Servicio SOAP (Semana 9)

### Endpoint
```
POST http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%209/soap_server.php
```

### WSDL
```
GET http://localhost/goapple/.../Semana%209/soap_server.php?wsdl
```

### Cabeceras
```
Content-Type: text/xml; charset=UTF-8
SOAPAction: http://goapple.local/ConsultarProducto
```

### Operación: `ConsultarProducto`

**Petición SOAP:**
```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:tns="http://goapple.local/wsdl">
  <soap:Header/>
  <soap:Body>
    <tns:ConsultarProductoRequest>
      <sku>1</sku>
    </tns:ConsultarProductoRequest>
  </soap:Body>
</soap:Envelope>
```

**Respuesta Exitosa:**
```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:tns="http://goapple.local/wsdl">
  <soap:Body>
    <tns:ConsultarProductoResponse>
      <id>1</id>
      <modelo>iPhone 16 Pro Max</modelo>
      <capacidad>512GB</capacidad>
      <color>Titanio Negro</color>
      <imei>123456789012345</imei>
      <precio>7200000.00</precio>
      <stock>1</stock>
      <condicion>nuevo</condicion>
      <estado>disponible</estado>
    </tns:ConsultarProductoResponse>
  </soap:Body>
</soap:Envelope>
```

**Respuesta de Error:**
```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>Server</faultcode>
      <faultstring>Producto no encontrado con SKU: XYZ</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
```

---

## 3. Service Registry (Semana 10)

### Archivo de Registro
```json
{
  "environment": "local",
  "node": "localhost",
  "base_url": "http://localhost/goapple",
  "services": [
    {
      "name": "GoAppleSoapService",
      "version": "1.0.0",
      "wsdl": "http://localhost/goapple/.../Semana%209/soap_server.php?wsdl",
      "endpoint": "http://localhost/goapple/.../Semana%209/soap_server.php",
      "protocol": "SOAP/HTTP",
      "operations": ["ConsultarProducto"]
    },
    {
      "name": "GoAppleXmlProtocol",
      "version": "1.0.0",
      "endpoint": "http://localhost/goapple/.../Semana%208/servidor.php",
      "protocol": "XML/HTTP",
      "operations": ["consultar_usuario", "consultar_producto",
                     "listar_productos", "consultar_cliente"]
    }
  ]
}
```

### Consumidor Virtualizado
El archivo `consumidor_virtualizado.php` lee el `service_registry.json`, selecciona un servicio y realiza una petición SOAP contra el endpoint registrado, demostrando el patrón de **Service Discovery** virtualizado.

---

## 4. Esquema de Autenticación

El sistema GoApple POS utiliza **autenticación por sesión PHP**:

1. El usuario envía credenciales (email + password) vía POST a `login.php`
2. El sistema verifica contra la tabla `usuarios` usando `password_verify()` (bcrypt)
3. Se crea una sesión PHP con `$_SESSION['usuario_id']`, `$_SESSION['usuario_rol']`
4. Timeout de sesión: 2 horas de inactividad

### Roles
| Rol | Permisos |
|-----|----------|
| **administrador** | Acceso total: CRUD, reportes, configuración, eliminación de registros |
| **vendedor** | Ventas, consultas, gestión de clientes (sin eliminar) |
