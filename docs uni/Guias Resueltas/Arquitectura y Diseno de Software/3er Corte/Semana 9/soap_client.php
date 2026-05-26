<?php

declare(strict_types=1);

header("Content-Type: text/html; charset=UTF-8");

$sku = $_GET['sku'] ?? '';

if (!$sku) {
    ?>
    <h2>Cliente SOAP - GoApple POS (Semana 9)</h2>
    <p>Consume el servicio SOAP de consulta de productos.</p>
    <form method="get">
        <label>SKU (ID o IMEI del iPhone):</label>
        <input type="text" name="sku" value="1" required>
        <button type="submit">Consultar</button>
    </form>
    <p style="margin-top:20px; font-size:0.9em;">Ejemplos: <code>1</code>, <code>APL-001</code> (IMEI demo)</p>
    <hr>
    <p><a href="?wsdl" target="_blank">Ver WSDL</a> | <a href="soap_server.php" target="_blank">Ver estado del servidor SOAP</a></p>
    <?php
    exit;
}

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

$endpoint = 'http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%209/soap_server.php';
$soapXml = buildRequest($sku);

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

echo "<h2>Cliente SOAP - Resultado</h2>";

if ($response === false) {
    echo "<p style='color:red;'>No fue posible consumir el endpoint SOAP.</p>";
    echo "<p><a href='?'>Volver</a></p>";
    exit;
}

echo "<h3>XML de Petición:</h3>";
echo "<pre style='background:#222; color:#0f0; padding:10px; overflow:auto;'>" . htmlspecialchars($soapXml) . "</pre>";

echo "<h3>XML de Respuesta:</h3>";
echo "<pre style='background:#222; color:#0cf; padding:10px; overflow:auto;'>" . htmlspecialchars($response) . "</pre>";

libxml_use_internal_errors(true);
$doc = new DOMDocument();
if ($doc->loadXML($response)) {
    $xp = new DOMXPath($doc);
    $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xp->registerNamespace('tns', 'http://goapple.local/wsdl');

    $modelo = $xp->evaluate('string(//tns:modelo)');
    $precio = $xp->evaluate('string(//tns:precio)');
    $stock = $xp->evaluate('string(//tns:stock)');

    echo "<h3>Datos del Producto:</h3>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    foreach (['id', 'modelo', 'capacidad', 'color', 'imei', 'precio', 'stock', 'condicion', 'estado'] as $campo) {
        $valor = $xp->evaluate("string(//tns:$campo)");
        echo "<tr><td>$campo</td><td>" . htmlspecialchars($valor) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>No se pudo parsear la respuesta SOAP.</p>";
}

echo "<p><a href='?'>Nueva consulta</a></p>";

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'soap_request.xml', $soapXml);
file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'soap_response.xml', $response);
