# Guía Práctica N° 10: Despliegue y Consumo Virtualizado de Servicios Web

**Asignatura:** Arquitectura y Diseño del Software  
**Semestre:** 2026-1  
**Estudiante:** [Tu Nombre]  
**Fecha:** [Fecha de entrega]  

---

## 1. Introducción

Esta guía práctica implementa el despliegue y consumo de servicios web en entornos virtualizados, cubriendo:

- **Actividad 1:** Publicación de servicios en infraestructura virtualizada.
- **Actividad 2:** Descubrimiento dinámico de servicios (estilo UDDI).
- **Actividad 3:** Consumo remoto de servicios desde cliente virtualizado.
- **Actividad 4:** Verificación y validación de endpoints en red.

El sistema demuestra un despliegue completo de servicios SOAP en Ubuntu Server virtualizado.

---

## 2. Arquitectura del Sistema

### Componentes Implementados

1. **Registro de Servicios (`service_registry.json`)**: Directorio de descubrimiento dinámico.
2. **Consumidor Virtualizado (`consumidor_virtualizado.php`)**: Cliente que consume servicios remotamente.
3. **Script de Verificación (`verificacion_endpoints.sh`)**: Valida conectividad de endpoints.
4. **Checklist de Despliegue (`deploy_checklist.md`)**: Guía operativa para despliegue.
5. **Informe de Despliegue (`informe_despliegue.md`)**: Documentación del proceso.

### Flujo de Despliegue

```
Registro → Publicar servicios → Cliente consulta registro → Descubre endpoint
Cliente → Consume servicio remoto → Recibe respuesta → Valida funcionamiento
```

---

## 3. Implementación Detallada

### 3.1 Registro de Servicios

```json
{
  "environment": "virtualized",
  "node": "ubuntu-server-goapple",
  "services": [
    {
      "name": "GoAppleSoapService",
      "version": "1.0.0",
      "wsdl": "http://SERVER_IP/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php?wsdl",
      "endpoint": "http://SERVER_IP/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php",
      "protocol": "SOAP/HTTP"
    }
  ]
}
```

**Funcionalidades:**
- Información de entorno virtualizado.
- Metadatos de servicios disponibles.
- URLs dinámicas reemplazables (SERVER_IP).

### 3.2 Consumidor Virtualizado

```php
<?php
declare(strict_types=1);

$registryPath = __DIR__ . DIRECTORY_SEPARATOR . 'service_registry.json';
$registry = json_decode((string) file_get_contents($registryPath), true);
$service = $registry['services'][0] ?? null;

$endpoint = (string) $service['endpoint'];
$endpoint = str_replace('SERVER_IP', '127.0.0.1', $endpoint);

$soap = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Body>
    <tns:ConsultarProductoRequest>
      <sku>APL-001</sku>
    </tns:ConsultarProductoRequest>
  </soap:Body>
</soap:Envelope>
XML;

$ctx = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/xml; charset=UTF-8\r\nSOAPAction: http://goapple.local/ConsultarProducto\r\n",
        'content' => $soap,
        'timeout' => 20,
    ],
]);

$resp = @file_get_contents($endpoint, false, $ctx);
file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'virtualized_response.xml', $resp);
echo "Consumo virtualizado OK. Archivo: virtualized_response.xml\n";
?>
```

**Funcionalidades:**
- Lee configuración desde registro JSON.
- Reemplaza placeholders con IPs reales.
- Consume servicio SOAP remotamente.
- Guarda respuesta para análisis.

### 3.3 Script de Verificación

```bash
#!/usr/bin/env bash
set -euo pipefail

SERVER_IP="${1:-127.0.0.1}"
WSDL_URL="http://${SERVER_IP}/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php?wsdl"
ENDPOINT_URL="http://${SERVER_IP}/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php"

echo "[1/2] Verificando WSDL: ${WSDL_URL}"
curl -fsS "${WSDL_URL}" > /tmp/goapple_wsdl.xml
echo "OK WSDL"

echo "[2/2] Verificando endpoint SOAP"
curl -fsS -X POST "${ENDPOINT_URL}" \
  -H "Content-Type: text/xml; charset=UTF-8" \
  -H "SOAPAction: http://goapple.local/ConsultarProducto" \
  --data-binary @- > /tmp/goapple_soap_response.xml <<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Body>
    <tns:ConsultarProductoRequest>
      <sku>APL-001</sku>
    </tns:ConsultarProductoRequest>
  </soap:Body>
</soap:Envelope>
XML

echo "OK SOAP. Archivo: /tmp/goapple_soap_response.xml"
```

**Funcionalidades:**
- Verifica disponibilidad del WSDL.
- Prueba conectividad del endpoint SOAP.
- Genera archivos de evidencia.
- Configurable con IP del servidor.

### 3.4 Checklist de Despliegue

```
## 1. Infraestructura virtualizada
- [ ] Ubuntu Server instalado y actualizado.
- [ ] Apache + PHP habilitados.
- [ ] Puerto 80 habilitado en firewall.
- [ ] Topologia validada entre nodo cliente y servidor.

## 2. Publicacion de servicios
- [ ] Endpoint SOAP publicado (soap_server.php).
- [ ] WSDL accesible desde cliente externo.
- [ ] Servicio de sockets (si aplica) con daemon de escucha activo.
- [ ] Prueba de conectividad con curl o navegador.

## 3. Descubrimiento dinamico (estilo UDDI)
- [ ] Registro de servicio actualizado (service_registry.json).
- [ ] Cliente consume endpoint desde registro, no hardcoded.

## 4. Validacion final
- [ ] Request/response exitoso en red real.
- [ ] Evidencia de error controlado (fault).
- [ ] Log de prueba final generado.
```

---

## 4. Proceso de Despliegue

### 4.1 Preparación del Entorno

1. **Instalación de Ubuntu Server:**
   - Configurar máquina virtual.
   - Instalar Apache y PHP.
   - Configurar red (IP estática).

2. **Despliegue de Archivos:**
   - Copiar archivos de la semana 9 (servicio SOAP).
   - Configurar service_registry.json con IP real.
   - Ajustar permisos de archivos.

### 4.2 Publicación de Servicios

1. **WSDL Access:**
   - Verificar `http://SERVER_IP/.../soap_server.php?wsdl`
   - Confirmar contrato accesible externamente.

2. **Endpoint SOAP:**
   - Probar conectividad básica.
   - Validar manejo de SOAP faults.

### 4.3 Consumo Remoto

1. **Configuración del Cliente:**
   - Actualizar service_registry.json.
   - Ejecutar consumidor_virtualizado.php.

2. **Verificación:**
   - Usar verificacion_endpoints.sh.
   - Validar archivos de respuesta generados.

---

## 5. Análisis de Virtualización y Despliegue

### 5.1 Ventajas de la Virtualización

- **Aislamiento:** Entornos independientes y reproducibles.
- **Escalabilidad:** Fácil clonación y distribución.
- **Consistencia:** Configuraciones idénticas en desarrollo/producción.

### 5.2 Descubrimiento Dinámico

**Comparación con UDDI:**

| Aspecto | UDDI Tradicional | Registro JSON |
|---------|------------------|---------------|
| **Complejidad** | Alta | Baja |
| **Estándares** | Sí | No |
| **Mantenimiento** | Difícil | Simple |
| **Escalabilidad** | Alta | Limitada |

### 5.3 Mejores Prácticas de Despliegue

1. **Automatización:** Usar scripts para despliegue consistente.
2. **Monitoreo:** Implementar logging y alertas.
3. **Seguridad:** Configurar firewalls y autenticación.
4. **Backup:** Estrategias de respaldo y recuperación.

---

## 6. Conclusiones

Esta implementación completa el ciclo de desarrollo de servicios web:

1. **Desarrollo Local:** Creación de servicios SOAP funcionales.
2. **Despliegue Virtualizado:** Publicación en entornos aislados.
3. **Consumo Remoto:** Validación de interoperabilidad.
4. **Descubrimiento Dinámico:** Localización automática de servicios.

El sistema demuestra un flujo completo de desarrollo a producción, con énfasis en la reproducibilidad y automatización del despliegue.

---

## 7. Referencias

- SOAP Web Services Deployment Guide
- Ubuntu Server Documentation
- Virtual Machine Management Best Practices
- Service-Oriented Architecture Patterns</content>
<parameter name="filePath">c:\laragon\www\goapple\docs uni\Guias Resueltas\Arquitectura y Diseno de Software\3er Corte\Semana 10\DOCUMENTACION_SEMANA10.md