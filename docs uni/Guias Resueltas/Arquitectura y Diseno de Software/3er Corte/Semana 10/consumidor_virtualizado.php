<?php

declare(strict_types=1);

header("Content-Type: text/html; charset=UTF-8");

$registryPath = __DIR__ . DIRECTORY_SEPARATOR . 'service_registry.json';
if (!file_exists($registryPath)) {
    echo "<p style='color:red;'>No existe service_registry.json</p>";
    exit(1);
}

$registry = json_decode((string) file_get_contents($registryPath), true);
$services = $registry['services'] ?? [];

if (empty($services)) {
    echo "<p style='color:red;'>Registro de servicios vacío.</p>";
    exit(1);
}

$sku = $_GET['sku'] ?? '1';

echo "<h2>Consumidor Virtualizado - GoApple POS (Semana 10)</h2>";
echo "<p>Consume los servicios registrados en el <em>service_registry.json</em></p>";

echo "<h3>Servicios Registrados:</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr><th>Nombre</th><th>Endpoint</th><th>Protocolo</th></tr>";
foreach ($services as $svc) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($svc['name']) . "</td>";
    echo "<td><code>" . htmlspecialchars($svc['endpoint']) . "</code></td>";
    echo "<td>" . htmlspecialchars($svc['protocol']) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr><h3>Prueba de Consumo - Servicio SOAP</h3>";
echo "<form method='get'>";
echo "<label>SKU (ID del iPhone): </label>";
echo "<input type='text' name='sku' value='" . htmlspecialchars($sku) . "'>";
echo "<button type='submit'>Consultar</button>";
echo "</form>";

$soapService = null;
foreach ($services as $svc) {
    if ($svc['name'] === 'GoAppleSoapService') {
        $soapService = $svc;
        break;
    }
}

if (!$soapService) {
    echo "<p style='color:red;'>No se encontró el servicio GoAppleSoapService en el registro.</p>";
    exit(1);
}

$endpoint = (string) $soapService['endpoint'];

$soap = <<<XML
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

$ctx = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/xml; charset=UTF-8\r\nSOAPAction: http://goapple.local/ConsultarProducto\r\n",
        'content' => $soap,
        'timeout' => 20,
    ],
]);

$resp = @file_get_contents($endpoint, false, $ctx);

echo "<h3>Petición SOAP Enviada:</h3>";
echo "<pre style='background:#222; color:#0f0; padding:10px; overflow:auto;'>" . htmlspecialchars($soap) . "</pre>";

if ($resp === false) {
    echo "<p style='color:red;'>Fallo el consumo del servicio virtualizado: $endpoint</p>";
    exit(1);
}

echo "<h3>Respuesta del Servicio:</h3>";
echo "<pre style='background:#222; color:#0cf; padding:10px; overflow:auto;'>" . htmlspecialchars($resp) . "</pre>";

libxml_use_internal_errors(true);
$doc = new DOMDocument();
if ($doc->loadXML($resp)) {
    $xp = new DOMXPath($doc);
    $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xp->registerNamespace('tns', 'http://goapple.local/wsdl');

    echo "<h3>Datos del Producto:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    foreach (['id', 'modelo', 'capacidad', 'color', 'imei', 'precio', 'stock', 'condicion', 'estado'] as $campo) {
        $valor = $xp->evaluate("string(//tns:$campo)");
        echo "<tr><td><strong>$campo</strong></td><td>" . htmlspecialchars($valor) . "</td></tr>";
    }
    echo "</table>";
}

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'virtualized_response.xml', $resp);
echo "<p style='color:green;'>Respuesta guardada en: virtualized_response.xml</p>";
