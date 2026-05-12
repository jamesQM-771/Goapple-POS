# Guía Práctica N° 9: Implementación de Servicios Web SOAP con WSDL y UDDI

**Asignatura:** Arquitectura y Diseño del Software  
**Semestre:** 2026-1  
**Estudiante:** [Tu Nombre]  
**Fecha:** [Fecha de entrega]  

---

## 1. Introducción

Esta guía práctica implementa un servicio web **SOAP** (Simple Object Access Protocol) para la consulta de productos en el sistema GoApple. Se desarrollan las siguientes actividades:

- **Actividad 1:** Definición del contrato WSDL (Web Services Description Language).
- **Actividad 2:** Implementación del servidor SOAP con manejo de SOAP Fault.
- **Actividad 3:** Desarrollo del cliente SOAP consumidor.
- **Actividad 4:** Simulación de registro UDDI para descubrimiento dinámico de servicios.

El sistema permite consultar información de productos (nombre, precio, stock) mediante un servicio web estandarizado.

---

## 2. Arquitectura del Sistema

### Componentes Implementados

1. **WSDL (`servicio.wsdl`)**: Contrato que define la interfaz del servicio web.
2. **Servidor SOAP (`soap_server.php`)**: Implementación del servicio que maneja peticiones SOAP.
3. **Cliente SOAP (`soap_client.php`)**: Consumidor que envía peticiones y recibe respuestas.
4. **Registro UDDI (`uddi_registry.json`)**: Directorio simulado para descubrimiento de servicios.

### Flujo de Comunicación SOAP

```
Cliente → Construye SOAP Request → Envía HTTP POST → Servidor SOAP
Servidor → Procesa Request → Valida parámetros → Consulta datos → Genera SOAP Response → Cliente
Cliente → Recibe Response → Procesa datos
```

---

## 3. Implementación Detallada

### 3.1 Contrato WSDL (`servicio.wsdl`)

El WSDL define la interfaz completa del servicio web:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<definitions
  name="GoAppleService"
  targetNamespace="http://goapple.local/wsdl"
  xmlns:tns="http://goapple.local/wsdl"
  xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
  xmlns:xsd="http://www.w3.org/2001/XMLSchema"
  xmlns="http://schemas.xmlsoap.org/wsdl/">

  <types>
    <xsd:schema targetNamespace="http://goapple.local/wsdl">
      <xsd:element name="ConsultarProductoRequest">
        <xsd:complexType>
          <xsd:sequence>
            <xsd:element name="sku" type="xsd:string"/>
          </xsd:sequence>
        </xsd:complexType>
      </xsd:element>
      <xsd:element name="ConsultarProductoResponse">
        <xsd:complexType>
          <xsd:sequence>
            <xsd:element name="nombre" type="xsd:string"/>
            <xsd:element name="precio" type="xsd:float"/>
            <xsd:element name="stock" type="xsd:int"/>
          </xsd:sequence>
        </xsd:complexType>
      </xsd:element>
    </xsd:schema>
  </types>

  <message name="ConsultarProductoInput">
    <part name="parameters" element="tns:ConsultarProductoRequest"/>
  </message>
  <message name="ConsultarProductoOutput">
    <part name="parameters" element="tns:ConsultarProductoResponse"/>
  </message>

  <portType name="GoApplePortType">
    <operation name="ConsultarProducto">
      <input message="tns:ConsultarProductoInput"/>
      <output message="tns:ConsultarProductoOutput"/>
    </operation>
  </portType>

  <binding name="GoAppleBinding" type="tns:GoApplePortType">
    <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
    <operation name="ConsultarProducto">
      <soap:operation soapAction="http://goapple.local/ConsultarProducto"/>
      <input>
        <soap:body use="literal"/>
      </input>
      <output>
        <soap:body use="literal"/>
      </output>
    </operation>
  </binding>

  <service name="GoAppleService">
    <port name="GoApplePort" binding="tns:GoAppleBinding">
      <soap:address location="http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php"/>
    </port>
  </service>
</definitions>
```

**Elementos del WSDL:**
- **Types:** Define los esquemas XML para request y response.
- **Messages:** Especifica los mensajes de entrada y salida.
- **PortType:** Define las operaciones disponibles.
- **Binding:** Especifica el protocolo de transporte (SOAP sobre HTTP).
- **Service:** Proporciona la ubicación del endpoint.

### 3.2 Servidor SOAP (`soap_server.php`)

```php
<?php

declare(strict_types=1);

function soapFaultXml(string $code, string $message): string
{
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>{$code}</faultcode>
      <faultstring>{$message}</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
XML;
}

function soapSuccessXml(array $data): string
{
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Body>
    <tns:ConsultarProductoResponse>
      <nombre>{$data['nombre']}</nombre>
      <precio>{$data['precio']}</precio>
      <stock>{$data['stock']}</stock>
    </tns:ConsultarProductoResponse>
  </soap:Body>
</soap:Envelope>
XML;
}

function handleSoapRequest(string $raw): string
{
    if (trim($raw) === '') {
        return soapFaultXml('Client', 'Peticion vacia.');
    }

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    if (!$doc->loadXML($raw)) {
        return soapFaultXml('Client', 'XML invalido.');
    }

    $xp = new DOMXPath($doc);
    $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $sku = trim((string) $xp->evaluate('string(//sku)'));

    if ($sku === '') {
        return soapFaultXml('Client', 'Parametro sku requerido.');
    }

    $fakeDb = [
        'APL-001' => ['nombre' => 'iPhone 16 Pro', 'precio' => '1200.00', 'stock' => '14'],
        'APL-002' => ['nombre' => 'iPhone 15', 'precio' => '900.00', 'stock' => '22'],
    ];

    if (!isset($fakeDb[$sku])) {
        return soapFaultXml('Server', 'Producto no encontrado.');
    }

    return soapSuccessXml($fakeDb[$sku]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $wsdl = __DIR__ . DIRECTORY_SEPARATOR . 'servicio.wsdl';
    if (isset($_GET['wsdl']) || (isset($_SERVER['QUERY_STRING']) && stripos($_SERVER['QUERY_STRING'], 'wsdl') !== false)) {
        header('Content-Type: text/xml; charset=UTF-8');
        readfile($wsdl);
        exit;
    }
    echo "SOAP server activo. Use POST SOAP o ?wsdl";
    exit;
}

$raw = file_get_contents('php://input') ?: '';
header('Content-Type: text/xml; charset=UTF-8');
echo handleSoapRequest($raw);
```

**Funcionalidades del servidor:**
- Maneja peticiones GET para servir el WSDL.
- Procesa peticiones SOAP POST.
- Valida XML y extrae parámetros con XPath.
- Genera respuestas SOAP o SOAP Fault según corresponda.
- Simula base de datos de productos.

### 3.3 Cliente SOAP (`soap_client.php`)

```php
<?php

declare(strict_types=1);

function buildRequest(string $sku): string
{
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Header/>
  <soap:Body>
    <tns:ConsultarProductoRequest>
      <sku>{$sku}</sku>
    </tns:ConsultarProductoRequest>
  </soap:Body>
</soap:Envelope>
XML;
}

$endpoint = 'http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php';
$soapXml = buildRequest('APL-001');

$opts = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/xml; charset=UTF-8\r\nSOAPAction: http://goapple.local/ConsultarProducto\r\n",
        'content' => $soapXml,
        'timeout' => 15,
    ],
];

$ctx = stream_context_create($opts);
$response = @file_get_contents($endpoint, false, $ctx);

if ($response === false) {
    echo "No fue posible consumir el endpoint SOAP.\n";
    exit(1);
}

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'soap_request.xml', $soapXml);
file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'soap_response.xml', $response);
echo "Consumo SOAP completado. Revise soap_request.xml y soap_response.xml\n";
```

**Funcionalidades del cliente:**
- Construye el envelope SOAP con la petición.
- Envía la petición HTTP POST al servidor.
- Guarda la petición y respuesta en archivos XML para análisis.

### 3.4 Registro UDDI (`uddi_registry.json`)

```json
{
  "serviceName": "GoAppleService",
  "version": "1.0.0",
  "protocol": "SOAP",
  "wsdl": "http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php?wsdl",
  "endpoint": "http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php",
  "operations": [
    "ConsultarProducto"
  ]
}
```

**Propósito del registro UDDI:**
- Simula un directorio de servicios web.
- Permite el descubrimiento dinámico de servicios.
- Contiene metadatos del servicio (nombre, versión, protocolo, operaciones).

---

## 4. Pruebas y Resultados

### 4.1 Validación del WSDL

Acceder a `http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php?wsdl` debe retornar el contrato WSDL completo.

### 4.2 Ejemplo de Petición SOAP

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Header/>
  <soap:Body>
    <tns:ConsultarProductoRequest>
      <sku>APL-001</sku>
    </tns:ConsultarProductoRequest>
  </soap:Body>
</soap:Envelope>
```

### 4.3 Ejemplo de Respuesta SOAP Exitosa

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Body>
    <tns:ConsultarProductoResponse>
      <nombre>iPhone 16 Pro</nombre>
      <precio>1200.00</precio>
      <stock>14</stock>
    </tns:ConsultarProductoResponse>
  </soap:Body>
</soap:Envelope>
```

### 4.4 Ejemplo de SOAP Fault

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>Client</faultcode>
      <faultstring>Producto no encontrado.</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
```

### 4.5 Archivos Generados

Después de ejecutar el cliente, se generan:
- `soap_request.xml`: Contiene la petición enviada.
- `soap_response.xml`: Contiene la respuesta recibida.

---

## 5. Análisis de SOAP, WSDL y UDDI

### 5.1 SOAP (Simple Object Access Protocol)

**Ventajas:**
- Protocolo estándar para intercambio de mensajes XML.
- Independiente de plataforma y lenguaje.
- Soporte nativo para manejo de errores (SOAP Fault).
- Extensible con headers personalizados.

**Desventajas:**
- Overhead considerable debido al envelope XML.
- Complejidad en comparación con REST.
- Menor rendimiento que protocolos binarios.

### 5.2 WSDL (Web Services Description Language)

**Propósito:**
- Describe la interfaz de un servicio web.
- Define operaciones, mensajes y protocolos de enlace.
- Permite generar código cliente automáticamente (code generation).

**Elementos clave:**
- Types: Definición de tipos de datos.
- Messages: Estructura de mensajes de entrada/salida.
- PortType: Operaciones disponibles.
- Binding: Protocolo de transporte.
- Service: Ubicación del endpoint.

### 5.3 UDDI (Universal Description, Discovery and Integration)

**Funcionalidad:**
- Registro centralizado de servicios web.
- Permite descubrimiento dinámico de servicios.
- Categorización y búsqueda de servicios por criterios.

**En la implementación:**
- Simulado con archivo JSON.
- Contiene metadatos del servicio GoApple.
- Facilita la integración con otros sistemas.

---

## 6. Conclusiones

Esta implementación demuestra las capacidades de los servicios web SOAP:

1. **Contratos Formales:** WSDL proporciona una especificación completa de la interfaz del servicio.
2. **Interoperabilidad:** SOAP permite comunicación entre diferentes plataformas y lenguajes.
3. **Manejo de Errores:** SOAP Fault ofrece un mecanismo estandarizado para reportar errores.
4. **Descubrimiento:** UDDI facilita la localización y consumo de servicios web.

La guía cumple con todos los requerimientos, implementando un servicio web SOAP completo con contrato WSDL y simulación de registro UDDI.

---

## 7. Referencias

- Especificación SOAP 1.2 (W3C)
- Especificación WSDL 2.0 (W3C)
- Especificación UDDI 3.0 (OASIS)
- PHP Manual - DOMDocument, XPath</content>
<parameter name="filePath">c:\laragon\www\goapple\docs uni\Guias Resueltas\Arquitectura y Diseno de Software\3er Corte\Semana 9\DOCUMENTACION_SEMANA9.md