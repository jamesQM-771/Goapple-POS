<?php

declare(strict_types=1);

$registryPath = __DIR__ . DIRECTORY_SEPARATOR . 'service_registry.json';
if (!file_exists($registryPath)) {
    echo "No existe service_registry.json\n";
    exit(1);
}

$registry = json_decode((string) file_get_contents($registryPath), true);
$service = $registry['services'][0] ?? null;

if (!$service || empty($service['endpoint'])) {
    echo "Registro de servicio invalido.\n";
    exit(1);
}

$endpoint = (string) $service['endpoint'];
$endpoint = str_replace('SERVER_IP', '127.0.0.1', $endpoint); // Ajuste local para prueba.

$soap = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Header/>
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
if ($resp === false) {
    echo "Fallo el consumo del servicio virtualizado: $endpoint\n";
    exit(1);
}

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'virtualized_response.xml', $resp);
echo "Consumo virtualizado OK. Archivo: virtualized_response.xml\n";
