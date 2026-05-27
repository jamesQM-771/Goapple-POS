<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: Giorgi Julian Ordoñez | Guía: 3 */
echo "<h1>🌐 Comunicación con API Externa — GoApple POS</h1>";

require_once __DIR__ . '/../../../../../config/database.php';

echo "<p>Consultando datos desde el servicio SOAP de GoApple...</p>";

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

$endpoint = "http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%209/soap_server.php";

$ctx = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/xml; charset=UTF-8\r\nSOAPAction: http://goapple.local/ConsultarProducto\r\n",
        'content' => $soapXml,
        'timeout' => 15,
    ],
]);

$response = @file_get_contents($endpoint, false, $ctx);

echo "<h2>📡 Petición Enviada (XML):</h2>";
echo "<pre style='background:#222; color:#0f0; padding:10px;'>" . htmlspecialchars($soapXml) . "</pre>";

if ($response === false) {
    echo "<p style='color:red;'>❌ Error: No se pudo conectar al servicio SOAP.</p>";
    echo "<p>Asegúrate de que Laragon esté corriendo.</p>";
} else {
    echo "<h2>📩 Respuesta del Servicio:</h2>";
    echo "<pre style='background:#222; color:#0cf; padding:10px;'>" . htmlspecialchars($response) . "</pre>";

    $doc = new DOMDocument();
    if ($doc->loadXML($response)) {
        $xp = new DOMXPath($doc);
        $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xp->registerNamespace('tns', 'http://goapple.local/wsdl');

        echo "<h2>📋 Datos del Producto:</h2>";
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        foreach (['id', 'modelo', 'capacidad', 'color', 'imei', 'precio', 'stock', 'condicion', 'estado'] as $campo) {
            $valor = $xp->evaluate("string(//tns:$campo)");
            echo "<tr><td><strong>" . ucfirst($campo) . "</strong></td><td>" . htmlspecialchars($valor) . "</td></tr>";
        }
        echo "</table>";
    }
}

echo "<hr><p><strong>Estado:</strong> Comunicación estandarizada correctamente mediante SOAP/XML.</p>";
echo "<p><a href='?sku=2'>Consultar otro producto (ID=2)</a> | <a href='?sku=APL-001'>Consultar por SKU</a></p>";

echo "<hr><h2>📊 Datos locales desde la BD (GoApple POS):</h2>";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, modelo, capacidad, color, precio_venta, estado FROM iphones ORDER BY id DESC LIMIT 5");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($productos) {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Modelo</th><th>Capacidad</th><th>Color</th><th>Precio</th><th>Estado</th></tr>";
        foreach ($productos as $p) {
            echo "<tr>";
            echo "<td>" . $p['id'] . "</td>";
            echo "<td>" . htmlspecialchars($p['modelo']) . "</td>";
            echo "<td>" . htmlspecialchars($p['capacidad']) . "</td>";
            echo "<td>" . htmlspecialchars($p['color']) . "</td>";
            echo "<td>$" . number_format($p['precio_venta'], 0, ',', '.') . "</td>";
            echo "<td>" . $p['estado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay productos en el inventario.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
