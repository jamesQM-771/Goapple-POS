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
