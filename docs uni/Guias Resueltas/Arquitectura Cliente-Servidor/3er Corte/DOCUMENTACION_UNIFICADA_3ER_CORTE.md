# Documentación Unificada: Arquitectura Cliente-Servidor - 3er Corte

**Asignatura:** Arquitectura y Diseño del Software  
**Semestre:** 2026-1  
**Estudiante:** [Tu Nombre]  
**Fecha:** [Fecha de entrega]  

---

## Índice

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Corte 1: Fundamentos de Arquitectura Cliente-Servidor](#2-corte-1-fundamentos-de-arquitectura-cliente-servidor)
3. [Corte 2: Comunicación y Protocolos Avanzados](#3-corte-2-comunicación-y-protocolos-avanzados)
4. [Corte 3: Servicios Web y Serialización](#4-corte-3-servicios-web-y-serialización)
5. [Conclusiones Generales](#5-conclusiones-generales)
6. [Referencias](#6-referencias)

---

## 1. Resumen Ejecutivo

Esta documentación unificada presenta el desarrollo completo de la materia **Arquitectura Cliente-Servidor** a través de tres cortes, culminando en el 3er corte con la implementación de servicios web SOAP y protocolos de serialización XML.

### Objetivos Alcanzados por Corte

**Corte 1:** Establecimiento de fundamentos teóricos y prácticos de la arquitectura cliente-servidor, incluyendo modelado de procesos, topologías de red y protocolos básicos de comunicación.

**Corte 2:** Desarrollo de mecanismos avanzados de comunicación, incluyendo callbacks, paso de parámetros remotos, persistencia de objetos y arquitectura distribuida.

**Corte 3:** Implementación de servicios web estandarizados (SOAP/WSDL/UDDI) y protocolos de serialización (XML con XSD/XSLT), incluyendo transformación de datos y validación estructural.

### Tecnologías Implementadas

- **Lenguajes:** PHP, JavaScript, XML, XSD, XSLT, WSDL
- **Protocolos:** HTTP, SOAP, XML-RPC
- **Herramientas:** DOMDocument, XPath, XSLTProcessor, cURL
- **Arquitecturas:** Cliente-Servidor, Servicios Web, Serialización

---

## 2. Corte 1: Fundamentos de Arquitectura Cliente-Servidor

### Semana 1: Delimitación de Procesos de Negocio Remotos

**Objetivos:**
- Identificar procesos críticos que deben ejecutarse en el servidor
- Diseñar la lógica de negocio centralizada
- Definir la estructura de payload para comunicación en red

**Implementación:**
```php
class GoAppleServerLogic {
    public function validarTransaccion($datos) {
        // Lógica de servidor simulada
        return true;
    }
}

class NetworkPayload {
    public string $accion;        // Ej: 'LOGIN', 'SYNC'
    public int $longitudCuerpo;   // Longitud del mensaje
    public string $cuerpoJSON;    // Datos estructurados
    public string $delimitador = "\r\n\r\n"; // Fin del mensaje
}
```

**Resultados:** Definición clara de responsabilidades entre cliente y servidor, con payload estructurado para comunicación segura.

### Semana 2: Modelado Lógico y Topologías de Red

**Objetivos:**
- Diseñar topologías de red apropiadas para el sistema
- Modelar lógicamente la arquitectura distribuida
- Evaluar ventajas y desventajas de diferentes configuraciones

**Topologías Implementadas:**
- **Estrella:** Cliente central con múltiples servidores especializados
- **Anillo:** Comunicación peer-to-peer entre nodos
- **Híbrida:** Combinación de topologías según necesidades

**Resultados:** Arquitectura híbrida seleccionada para GoApple, optimizando rendimiento y escalabilidad.

### Semana 3: Diseño de Interfaces y Contratos

**Objetivos:**
- Definir interfaces claras entre componentes
- Establecer contratos de comunicación
- Diseñar APIs preliminares

**Implementación:** Interfaces PHP con métodos definidos y contratos documentados.

### Semana 4: Implementación Básica Cliente-Servidor

**Objetivos:**
- Desarrollar primera versión funcional del sistema
- Implementar comunicación básica
- Validar conectividad entre componentes

**Resultados:** Sistema básico operativo con comunicación HTTP fundamental.

---

## 3. Corte 2: Comunicación y Protocolos Avanzados

### Semana 5: Callbacks y Comunicación Asíncrona

**Objetivos:**
- Implementar mecanismos de callback
- Desarrollar comunicación asíncrona
- Gestionar eventos y respuestas diferidas

**Implementación:** Sistema de callbacks en PHP para notificaciones y actualizaciones en tiempo real.

### Semana 6: Paso de Parámetros Remotos

**Objetivos:**
- Diseñar serialización de parámetros complejos
- Implementar paso de objetos por referencia
- Gestionar marshaling/unmarshaling de datos

**Técnicas Implementadas:**
- Serialización JSON para objetos simples
- Referencias remotas para objetos complejos
- Validación de integridad de datos transmitidos

### Semana 7: Persistencia y Estado en Sistemas Distribuidos

**Objetivos:**
- Implementar persistencia de objetos distribuidos
- Gestionar estado en entornos multi-servidor
- Diseñar estrategias de replicación

**Implementación:** Sistema de persistencia con base de datos distribuida y sincronización de estado.

---

## 4. Corte 3: Servicios Web y Serialización

### Semana 8: Protocolos de Intercambio y Serialización XML

#### Arquitectura Implementada

**Componentes:**
1. **Cliente XML (`cliente.php`)**: Genera y valida mensajes XML
2. **Servidor XML (`servidor.php`)**: Procesa peticiones con XPath
3. **Esquema XSD (`protocolo.xsd`)**: Valida estructura de mensajes
4. **Transformación XSLT (`transformacion.xslt`)**: Convierte XML a HTML

#### Flujo de Comunicación

```
Cliente → Genera XML Request → Valida XSD → Envía HTTP POST → Servidor
Servidor → Recibe XML → Valida XSD → Procesa con XPath → Genera XML Response → Cliente
Cliente → Recibe Response → Transforma con XSLT → Muestra HTML
```

#### Esquema XSD Principal

```xml
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
```

#### Funcionalidades Clave

- **Validación Estructural:** XSD rechaza mensajes malformados
- **Procesamiento XPath:** Extracción precisa de datos del XML
- **Transformación XSLT:** Conversión XML a presentaciones visuales
- **Manejo de Errores:** Respuestas estructuradas para casos de error

#### Análisis Comparativo de Tecnologías

| Criterio | XML | JSON | Serialización Binaria |
|----------|-----|------|----------------------|
| Tamaño | Muy pesado | Ligero-moderado | Extremadamente ligero |
| Rendimiento | Lento | Muy rápido | El más rápido |
| Legibilidad | Moderada | Muy fácil | Difícil |
| Validación | Estricta (XSD) | Esquemas opcionales | Pre-compartida |

**Conclusión:** XML es ideal para entornos empresariales que requieren formalidad y validación estricta.

### Semana 9: Servicios Web SOAP con WSDL y UDDI

#### Arquitectura SOAP Implementada

**Componentes:**
1. **Contrato WSDL (`servicio.wsdl`)**: Define interfaz del servicio
2. **Servidor SOAP (`soap_server.php`)**: Implementa operaciones SOAP
3. **Cliente SOAP (`soap_client.php`)**: Consume el servicio web
4. **Registro UDDI (`uddi_registry.json`)**: Simula descubrimiento de servicios

#### Estructura WSDL

```xml
<definitions name="GoAppleService" targetNamespace="http://goapple.local/wsdl">
  <types>
    <!-- Definición de tipos de datos -->
  </types>
  <message name="ConsultarProductoInput">
    <!-- Mensajes de entrada/salida -->
  </message>
  <portType name="GoApplePortType">
    <!-- Operaciones disponibles -->
  </portType>
  <binding name="GoAppleBinding">
    <!-- Protocolo de enlace SOAP -->
  </binding>
  <service name="GoAppleService">
    <!-- Ubicación del endpoint -->
  </service>
</definitions>
```

#### Operación Implementada: ConsultarProducto

**Petición SOAP:**
```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ConsultarProductoRequest>
      <sku>APL-001</sku>
    </ConsultarProductoRequest>
  </soap:Body>
</soap:Envelope>
```

**Respuesta SOAP:**
```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ConsultarProductoResponse>
      <nombre>iPhone 16 Pro</nombre>
      <precio>1200.00</precio>
      <stock>14</stock>
    </ConsultarProductoResponse>
  </soap:Body>
</soap:Envelope>
```

#### Manejo de Errores SOAP Fault

```xml
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>Client</faultcode>
      <faultstring>Producto no encontrado.</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
```

#### Registro UDDI Simulado

```json
{
  "serviceName": "GoAppleService",
  "version": "1.0.0",
  "protocol": "SOAP",
  "wsdl": "http://localhost/goapple/.../servicio.wsdl",
  "endpoint": "http://localhost/goapple/.../soap_server.php",
  "operations": ["ConsultarProducto"]
}
```

#### Ventajas de SOAP/WSDL/UDDI

- **Interoperabilidad:** Estándar multiplataforma
- **Contratos Formales:** WSDL define interfaces precisas
- **Descubrimiento:** UDDI permite localización dinámica
- **Manejo de Errores:** SOAP Fault estandarizado

### Semana 10: Despliegue y Virtualización de Servicios

**Objetivos:**
- Implementar estrategias de despliegue
- Virtualizar servicios para escalabilidad
- Optimizar rendimiento en producción

**Implementaciones:**
- Contenedores Docker para aislamiento
- Balanceo de carga
- Monitoreo y logging de servicios

---

## 5. Conclusiones Generales

### Evolución Tecnológica

La materia ha demostrado una progresión lógica desde fundamentos básicos hasta tecnologías avanzadas de servicios web:

1. **Corte 1:** Estableció las bases teóricas y prácticas
2. **Corte 2:** Desarrolló comunicación avanzada y distribución
3. **Corte 3:** Implementó estándares industriales de interoperabilidad

### Lecciones Aprendidas

- **Importancia de Contratos:** WSDL y XSD proporcionan especificaciones claras
- **Validación es Crítica:** La validación temprana previene errores costosos
- **Interoperabilidad Primero:** Los estándares abiertos facilitan integración
- **Rendimiento vs. Formalidad:** Equilibrio entre velocidad y estructura

### Aplicabilidad en GoApple

El sistema desarrollado es directamente aplicable al negocio de GoApple:
- Consulta de productos vía servicios web
- Validación estricta de transacciones
- Interoperabilidad con sistemas externos
- Escalabilidad mediante virtualización

### Tecnologías Recomendadas

Para producción, se recomienda:
- **SOAP/WSDL** para contratos formales B2B
- **REST/JSON** para APIs públicas de alto rendimiento
- **GraphQL** para consultas flexibles
- **gRPC** para comunicación interna de microservicios

---

## 6. Referencias

### Especificaciones Técnicas
- SOAP 1.2 (W3C)
- WSDL 2.0 (W3C)
- UDDI 3.0 (OASIS)
- XML Schema 1.0 (W3C)
- XSLT 1.0 (W3C)

### Recursos PHP
- PHP Manual - DOMDocument
- PHP Manual - XPath
- PHP Manual - XSLTProcessor
- PHP Manual - cURL

### Documentación del Proyecto
- Guías Prácticas Semanales (1-10)
- Informes Comparativos
- Diagramas de Arquitectura
- Casos de Prueba

---

**Fin de la Documentación Unificada - Arquitectura Cliente-Servidor 3er Corte**</content>
<parameter name="filePath">c:\laragon\www\goapple\docs uni\Guias Resueltas\Arquitectura Cliente-Servidor\3er Corte\DOCUMENTACION_UNIFICADA_3ER_CORTE.md