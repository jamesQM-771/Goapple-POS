# Guía Práctica N° 8: Diseño de Protocolos de Intercambio y Serialización XML en Sistemas Distribuidos

**Asignatura:** Arquitectura y Diseño del Software  
**Semestre:** 2026-1  
**Estudiante:** [Tu Nombre]  
**Fecha:** [Fecha de entrega]  

---

## 1. Introducción

Esta guía práctica implementa un sistema cliente-servidor utilizando **XML** como protocolo de serialización para el intercambio de datos. Se desarrollan las siguientes actividades:

- **Actividad 1:** Construcción de un mensaje XML de petición (request).
- **Actividad 2:** Validación de la estructura XML contra un esquema XSD.
- **Actividad 3:** Comunicación cliente-servidor mediante HTTP POST.
- **Actividad 4:** Procesamiento del XML en el servidor utilizando XPath.
- **Actividad 5:** Transformación de la respuesta XML a HTML utilizando XSLT.

El sistema simula una consulta de usuario en una aplicación de gestión (GoApple), donde el cliente envía una petición XML y el servidor responde con datos del usuario solicitado.

---

## 2. Arquitectura del Sistema

### Componentes Implementados

1. **Cliente (`cliente.php`)**: Genera el XML de petición, valida contra XSD, envía al servidor y transforma la respuesta.
2. **Servidor (`servidor.php`)**: Recibe el XML, valida, procesa con XPath y genera respuesta XML.
3. **Esquema XSD (`protocolo.xsd`)**: Define la estructura válida para mensajes XML.
4. **Transformación XSLT (`transformacion.xslt`)**: Convierte la respuesta XML en HTML presentable.

### Flujo de Comunicación

```
Cliente → Genera XML Request → Valida XSD → Envía HTTP POST → Servidor
Servidor → Recibe XML → Valida XSD → Procesa con XPath → Genera XML Response → Cliente
Cliente → Recibe Response → Transforma con XSLT → Muestra HTML
```

---

## 3. Implementación Detallada

### 3.1 Esquema XSD (`protocolo.xsd`)

El esquema define la estructura de mensajes XML válidos:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:element name="mensaje">
    <xs:complexType>
      <xs:sequence>
        <xs:element name="mensajes_control" type="ControlTipo"/>
        <xs:choice>
          <xs:element name="request" type="RequestTipo"/>
          <xs:element name="response" type="ResponseTipo"/>
          <xs:element name="error" type="ErrorTipo"/>
        </xs:choice>
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <!-- Tipos complejos -->
  <xs:complexType name="ControlTipo">
    <xs:sequence>
      <xs:element name="id_transaccion" type="xs:string"/>
      <xs:element name="timestamp" type="xs:dateTime"/>
      <xs:element name="emisor" type="xs:string" minOccurs="0"/>
    </xs:sequence>
  </xs:complexType>

  <xs:complexType name="RequestTipo">
    <xs:sequence>
      <xs:element name="operacion" type="xs:string"/>
      <xs:element name="datos" type="DatosTipo" minOccurs="0"/>
    </xs:sequence>
  </xs:complexType>

  <xs:complexType name="ResponseTipo">
    <xs:sequence>
      <xs:element name="operacion" type="xs:string"/>
      <xs:element name="estado" type="xs:string"/>
      <xs:element name="datos" type="DatosTipo" minOccurs="0"/>
    </xs:sequence>
  </xs:complexType>

  <xs:complexType name="ErrorTipo">
    <xs:sequence>
      <xs:element name="estado" type="xs:string"/>
      <xs:element name="codigo" type="xs:string"/>
      <xs:element name="mensaje_error" type="xs:string"/>
    </xs:sequence>
  </xs:complexType>

  <xs:complexType name="DatosTipo">
    <xs:sequence>
      <xs:element name="item" minOccurs="0" maxOccurs="unbounded">
        <xs:complexType>
          <xs:sequence>
            <xs:element name="clave" type="xs:string"/>
            <xs:element name="valor" type="xs:string"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
</xs:schema>
```

**Características del esquema:**
- Estructura jerárquica con elemento raíz `mensaje`.
- Sección de control obligatorio con ID de transacción, timestamp y emisor.
- Tres tipos de mensaje posibles: request, response o error.
- Tipo de datos genérico clave-valor para flexibilidad.

### 3.2 Cliente PHP (`cliente.php`)

```php
<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h2>Cliente XML - Guía 8</h2>";

// 1. Construir el XML de petición
$xml = new DOMDocument("1.0", "UTF-8");
$xml->formatOutput = true;

$root = $xml->createElement("mensaje");
$xml->appendChild($root);

// Mensajes de control
$ctrl = $xml->createElement("mensajes_control");
$ctrl->appendChild($xml->createElement("id_transaccion", uniqid()));
$ctrl->appendChild($xml->createElement("timestamp", date('c')));
$ctrl->appendChild($xml->createElement("emisor", "Cliente-PHP"));
$root->appendChild($ctrl);

// Request
$req = $xml->createElement("request");
$req->appendChild($xml->createElement("operacion", "consultar_usuario"));

$datosNode = $xml->createElement("datos");
$itemNode = $xml->createElement("item");
$itemNode->appendChild($xml->createElement("clave", "usuario_id"));
$itemNode->appendChild($xml->createElement("valor", "USR-999"));
$datosNode->appendChild($itemNode);
$req->appendChild($datosNode);
$root->appendChild($req);

$xmlString = $xml->saveXML();

// 2. Validar estructura antes de enviar
libxml_use_internal_errors(true);
if (!$xml->schemaValidate('protocolo.xsd')) {
    echo "<b style='color:red;'>Error: El XML generado no es válido según el XSD.</b><br>";
    $errores = libxml_get_errors();
    foreach ($errores as $error) {
        echo $error->message . "<br>";
    }
    libxml_clear_errors();
    exit;
} else {
    echo "<p style='color:green;'><b>[OK] Validación XSD local exitosa.</b></p>";
}

// 3. Enviar al servidor
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$basePath = "/goapple";
if (strpos($host, 'goapple.test') !== false) {
    $basePath = "";
}
$url = "http://" . $host . $basePath . "/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana 8/servidor.php";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);

$responseXML = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($responseXML === false || $httpCode !== 200) {
    echo "<b style='color:red;'>Error de comunicación con el servidor.</b><br>";
    exit;
}

// 4. Transformación XSLT
$docRespuesta = new DOMDocument();
if ($docRespuesta->loadXML($responseXML)) {
    $xsl = new DOMDocument();
    if (file_exists('transformacion.xslt') && $xsl->load('transformacion.xslt')) {
        $proc = new XSLTProcessor();
        $proc->importStyleSheet($xsl);
        $htmlResult = $proc->transformToXML($docRespuesta);
        echo "<div style='border: 2px solid #555; padding: 15px; background: #fff; color: #000;'>";
        echo $htmlResult;
        echo "</div>";
    } else {
        echo "<b style='color:red;'>Error: Archivo transformacion.xslt no encontrado.</b>";
    }
} else {
    echo "<b style='color:red;'>Error: El XML recibido es malformado.</b>";
}
?>
```

**Funcionalidades del cliente:**
- Genera XML dinámicamente con DOMDocument.
- Valida el XML contra el esquema XSD antes del envío.
- Utiliza cURL para comunicación HTTP POST con el servidor.
- Aplica transformación XSLT para presentación visual.

### 3.3 Servidor PHP (`servidor.php`)

```php
<?php
header("Content-Type: application/xml; charset=UTF-8");

$xmlInput = file_get_contents('php://input');

if (empty($xmlInput)) {
    echo generarError("FALLO", "ERR-01", "Cuerpo de la petición vacío.");
    exit;
}

$doc = new DOMDocument();
libxml_use_internal_errors(true);
if (!$doc->loadXML($xmlInput)) {
    echo generarError("FALLO", "ERR-02", "XML malformado.");
    exit;
}

// Validar contra protocolo.xsd
if (!$doc->schemaValidate('protocolo.xsd')) {
    $errores = libxml_get_errors();
    $mensajeError = "XML no cumple con el esquema: " . $errores[0]->message;
    libxml_clear_errors();
    echo generarError("FALLO", "ERR-03", $mensajeError);
    exit;
}

// Procesamiento con XPath
$xpath = new DOMXPath($doc);

$nodeRequest = $xpath->query("/mensaje/request");
if ($nodeRequest->length == 0) {
    echo generarError("FALLO", "ERR-04", "El mensaje no es una solicitud.");
    exit;
}

$idTransaccion = $xpath->query("/mensaje/mensajes_control/id_transaccion")->item(0)->nodeValue;
$operacion = $xpath->query("/mensaje/request/operacion")->item(0)->nodeValue;

$items = $xpath->query("/mensaje/request/datos/item");
$parametros = [];
foreach ($items as $item) {
    $clave = $xpath->query("clave", $item)->item(0)->nodeValue;
    $valor = $xpath->query("valor", $item)->item(0)->nodeValue;
    $parametros[$clave] = $valor;
}

// Procesamiento simulado
if ($operacion === "consultar_usuario") {
    $userId = isset($parametros['usuario_id']) ? $parametros['usuario_id'] : 'Desconocido';
    $respuestaItems = [
        "nombre" => "Juan Perez",
        "rol" => "Administrador",
        "usuario_id" => $userId
    ];
    $estado = "EXITO";
} else {
    echo generarError("FALLO", "ERR-05", "Operación no soportada.");
    exit;
}

echo generarResponse($idTransaccion, $operacion, $estado, $respuestaItems);

// Funciones auxiliares
function generarError($estado, $codigo, $mensaje) {
    $xml = new DOMDocument("1.0", "UTF-8");
    $xml->formatOutput = true;
    
    $root = $xml->createElement("mensaje");
    $xml->appendChild($root);
    
    $ctrl = $xml->createElement("mensajes_control");
    $ctrl->appendChild($xml->createElement("id_transaccion", "N/A"));
    $ctrl->appendChild($xml->createElement("timestamp", date('c')));
    $ctrl->appendChild($xml->createElement("emisor", "Servidor"));
    $root->appendChild($ctrl);
    
    $error = $xml->createElement("error");
    $error->appendChild($xml->createElement("estado", $estado));
    $error->appendChild($xml->createElement("codigo", $codigo));
    $error->appendChild($xml->createElement("mensaje_error", htmlspecialchars($mensaje)));
    $root->appendChild($error);
    
    return $xml->saveXML();
}

function generarResponse($idTransaccion, $operacion, $estado, $datos) {
    $xml = new DOMDocument("1.0", "UTF-8");
    $xml->formatOutput = true;
    
    $root = $xml->createElement("mensaje");
    $xml->appendChild($root);
    
    $ctrl = $xml->createElement("mensajes_control");
    $ctrl->appendChild($xml->createElement("id_transaccion", $idTransaccion));
    $ctrl->appendChild($xml->createElement("timestamp", date('c')));
    $ctrl->appendChild($xml->createElement("emisor", "Servidor"));
    $root->appendChild($ctrl);
    
    $resp = $xml->createElement("response");
    $resp->appendChild($xml->createElement("operacion", $operacion));
    $resp->appendChild($xml->createElement("estado", $estado));
    
    if (!empty($datos)) {
        $datosNode = $xml->createElement("datos");
        foreach ($datos as $k => $v) {
            $itemNode = $xml->createElement("item");
            $itemNode->appendChild($xml->createElement("clave", $k));
            $itemNode->appendChild($xml->createElement("valor", htmlspecialchars($v)));
            $datosNode->appendChild($itemNode);
        }
        $resp->appendChild($datosNode);
    }
    
    $root->appendChild($resp);
    
    return $xml->saveXML();
}
?>
```

**Funcionalidades del servidor:**
- Recibe XML vía POST.
- Valida estructura contra XSD.
- Utiliza XPath para extraer datos del XML.
- Genera respuestas XML estructuradas o errores.

### 3.4 Transformación XSLT (`transformacion.xslt`)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <div style="font-family: Arial, sans-serif;">
      <h2>Resultado de la Operación</h2>
      
      <div style="background-color: #eee; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
        <strong>ID Transacción:</strong> <xsl:value-of select="/mensaje/mensajes_control/id_transaccion"/><br/>
        <strong>Fecha/Hora:</strong> <xsl:value-of select="/mensaje/mensajes_control/timestamp"/><br/>
        <strong>Emisor:</strong> <xsl:value-of select="/mensaje/mensajes_control/emisor"/>
      </div>

      <xsl:choose>
        <xsl:when test="/mensaje/response">
          <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;">
            <h3>¡Respuesta del Servidor!</h3>
            <p><strong>Operación:</strong> <xsl:value-of select="/mensaje/response/operacion"/></p>
            <p><strong>Estado:</strong> <xsl:value-of select="/mensaje/response/estado"/></p>
            
            <xsl:if test="/mensaje/response/datos/item">
              <table style="width: 100%; border-collapse: collapse; margin-top: 10px; background: white;">
                <tr style="background-color: #c3e6cb;">
                  <th style="padding: 8px; border: 1px solid #b1dfbb; text-align: left;">Clave</th>
                  <th style="padding: 8px; border: 1px solid #b1dfbb; text-align: left;">Valor</th>
                </tr>
                <xsl:for-each select="/mensaje/response/datos/item">
                  <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><xsl:value-of select="clave"/></td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong><xsl:value-of select="valor"/></strong></td>
                  </tr>
                </xsl:for-each>
              </table>
            </xsl:if>
          </div>
        </xsl:when>
        
        <xsl:when test="/mensaje/error">
          <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
            <h3>Ocurrió un Error</h3>
            <p><strong>Estado:</strong> <xsl:value-of select="/mensaje/error/estado"/></p>
            <p><strong>Código:</strong> <xsl:value-of select="/mensaje/error/codigo"/></p>
            <p><strong>Mensaje:</strong> <xsl:value-of select="/mensaje/error/mensaje_error"/></p>
          </div>
        </xsl:when>
      </xsl:choose>
    </div>
  </xsl:template>
</xsl:stylesheet>
```

**Funcionalidades de la transformación:**
- Convierte XML de respuesta a HTML visual.
- Maneja tanto respuestas exitosas como errores.
- Presenta datos en tabla formateada.

---

## 4. Pruebas y Resultados

### 4.1 Ejemplo de XML Generado por el Cliente

```xml
<?xml version="1.0" encoding="UTF-8"?>
<mensaje>
  <mensajes_control>
    <id_transaccion>67a8b9c1d2e3f4</id_transaccion>
    <timestamp>2026-05-11T10:30:00-05:00</timestamp>
    <emisor>Cliente-PHP</emisor>
  </mensajes_control>
  <request>
    <operacion>consultar_usuario</operacion>
    <datos>
      <item>
        <clave>usuario_id</clave>
        <valor>USR-999</valor>
      </item>
    </datos>
  </request>
</mensaje>
```

### 4.2 Ejemplo de Respuesta del Servidor

```xml
<?xml version="1.0" encoding="UTF-8"?>
<mensaje>
  <mensajes_control>
    <id_transaccion>67a8b9c1d2e3f4</id_transaccion>
    <timestamp>2026-05-11T10:30:01-05:00</timestamp>
    <emisor>Servidor</emisor>
  </mensajes_control>
  <response>
    <operacion>consultar_usuario</operacion>
    <estado>EXITO</estado>
    <datos>
      <item>
        <clave>nombre</clave>
        <valor>Juan Perez</valor>
      </item>
      <item>
        <clave>rol</clave>
        <valor>Administrador</valor>
      </item>
      <item>
        <clave>usuario_id</clave>
        <valor>USR-999</valor>
      </item>
    </datos>
  </response>
</mensaje>
```

### 4.3 Resultado de la Transformación XSLT

La transformación XSLT convierte la respuesta XML en una página HTML visual con:
- Información de control de mensajes.
- Estado de la operación.
- Tabla con los datos del usuario consultado.

---

## 5. Análisis Comparativo de Tecnologías de Serialización

### 5.1 Tamaño del Mensaje (Payload Size)

- **XML:** Muy pesado debido a etiquetas de apertura y cierre.
- **JSON:** Ligero a moderado, sin etiquetas de cierre completas.
- **Serialización Binaria:** Extremadamente ligero, sin codificación de estructura en texto.

### 5.2 Rendimiento (Velocidad de Parsing/Serialización)

- **XML:** Lento, especialmente con validación XSD y transformaciones XSLT.
- **JSON:** Muy rápido con parsers nativos optimizados.
- **Serialización Binaria:** El más rápido, mapeo directo de bytes a estructuras.

### 5.3 Facilidad de Uso y Legibilidad

- **XML:** Moderado/complejo, pero con validación estricta XSD y transformaciones XSLT.
- **JSON:** Muy fácil y legible para humanos.
- **Serialización Binaria:** Difícil, no legible para humanos.

### 5.4 Conclusión

XML es ideal cuando priman la formalidad, documentación estricta y rigor empresarial sobre la velocidad. Para aplicaciones de alto rendimiento, JSON o serialización binaria son preferibles.

---

## 6. Conclusiones

Esta implementación demuestra las capacidades de XML como protocolo de serialización en sistemas distribuidos:

1. **Validación Estructural:** XSD proporciona validación estricta que rechaza mensajes inválidos.
2. **Flexibilidad:** El formato clave-valor permite adaptarse a diferentes tipos de datos.
3. **Transformaciones:** XSLT permite convertir XML a otros formatos sin modificar la lógica del sistema.
4. **Interoperabilidad:** XML es un estándar ampliamente soportado en diferentes plataformas.

La guía cumple con todos los requerimientos, implementando un sistema cliente-servidor funcional con validación, procesamiento y transformación de datos XML.

---

## 7. Referencias

- Especificación XML 1.0 (W3C)
- Especificación XSD 1.0 (W3C)
- Especificación XSLT 1.0 (W3C)
- PHP Manual - DOMDocument, XPath, XSLTProcessor</content>
<parameter name="filePath">c:\laragon\www\goapple\docs uni\Guias Resueltas\Arquitectura y Diseno de Software\3er Corte\Semana 8\DOCUMENTACION_SEMANA8.md