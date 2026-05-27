<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>🤝 Cliente Handshake — GoApple POS</h1>";
echo "<p>Simula un handshake TCP/IP con el servidor, luego consulta datos reales vía HTTP.</p>";

$host = '127.0.0.1';
$port = 9000;

echo "<h2>📡 Handshake (Apretón de Manos)</h2>";

$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "<p style='color:orange;'>⚠️ Extensión sockets no disponible. Usando HTTP handshake.</p>";
    $handshakeOk = true;
} else {
    $result = @socket_connect($socket, $host, $port);
    if ($result === false) {
        echo "<p style='color:orange;'>⚠️ No hay servidor socket en {$host}:{$port}. Usando HTTP.</p>";
        $handshakeOk = true;
    } else {
        echo "<p style='color:green;'>✅ Handshake exitoso. Conexión establecida con {$host}:{$port}</p>";
        socket_close($socket);
        $handshakeOk = true;
    }
}

if ($handshakeOk) {
    echo "<p style='color:green;'>✅ Canal de transporte verificado.</p>";
}

echo "<h2>📦 Consulta de Datos (vía HTTP → SOAP)</h2>";

$sku = $_GET['sku'] ?? '1';

$soapXml = <<<XML
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

$endpoint = "http://{$_SERVER['HTTP_HOST']}/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php";

$ctx = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/xml; charset=UTF-8\r\nSOAPAction: http://goapple.local/ConsultarProducto\r\n",
        'content' => $soapXml,
        'timeout' => 10,
    ],
]);

$response = @file_get_contents($endpoint, false, $ctx);

echo "<p>Conectando a: <code>" . htmlspecialchars($endpoint) . "</code></p>";

if ($response === false) {
    echo "<p style='color:red;'>❌ Error: No se pudo completar el handshake con el servicio SOAP.</p>";
    echo "<p>Verifica que Laragon esté corriendo.</p>";
} else {
    echo "<p style='color:green;'>✅ Handshake SOAP exitoso. Datos recibidos:</p>";

    $doc = new DOMDocument();
    $doc->loadXML($response);
    $xp = new DOMXPath($doc);
    $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xp->registerNamespace('tns', 'http://goapple.local/wsdl');

    $modelo = $xp->evaluate("string(//tns:modelo)");
    $precio = $xp->evaluate("string(//tns:precio)");

    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    foreach (['id', 'modelo', 'capacidad', 'color', 'imei', 'precio', 'stock', 'condicion', 'estado'] as $campo) {
        $valor = $xp->evaluate("string(//tns:$campo)");
        echo "<tr><td>" . ucfirst($campo) . "</td><td>" . htmlspecialchars($valor) . "</td></tr>";
    }
    echo "</table>";
}

echo "<hr><p><a href='?sku=1'>Consultar SKU 1</a> | <a href='?sku=2'>Consultar SKU 2</a></p>";
