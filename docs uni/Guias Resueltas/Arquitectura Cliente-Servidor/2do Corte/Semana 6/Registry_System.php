<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>📖 Sistema de Nombrado y Registro — GoApple POS</h1>";
echo "<p>Sistema de registro y descubrimiento dinámico de servicios (Service Directory).</p>";

$registryPath = __DIR__ . '/../../3er Corte/Semana 10/service_registry.json';

class RegistryDirectory {
    private $servicios = [];

    public function bind($nombreLogico, $direccionIP, $puerto, $protocolo = 'HTTP') {
        $this->servicios[$nombreLogico] = [
            'ip' => $direccionIP,
            'puerto' => $puerto,
            'protocolo' => $protocolo
        ];
        echo "<p style='color:green;'>✅ Servicio <strong>" . htmlspecialchars($nombreLogico) . "</strong> registrado en {$direccionIP}:{$puerto} ({$protocolo})</p>";
    }

    public function lookup($nombreLogico) {
        if (isset($this->servicios[$nombreLogico])) {
            return $this->servicios[$nombreLogico];
        }
        throw new Exception("Servicio '{$nombreLogico}' no encontrado en el registro.");
    }

    public function listar() {
        return $this->servicios;
    }
}

echo "<h2>1️⃣ Configuración del Servicio de Directorio (Registry)</h2>";

$registry = new RegistryDirectory();

$registry->bind("GoAppleAuth", $_SERVER['HTTP_HOST'] ?? 'localhost', 80, 'HTTP');
$registry->bind("GoAppleSoapService", $_SERVER['HTTP_HOST'] ?? 'localhost', 80, 'SOAP/HTTP');
$registry->bind("GoAppleXmlApi", $_SERVER['HTTP_HOST'] ?? 'localhost', 80, 'XML/HTTP');

echo "<h2>2️⃣ Servicios Registrados</h2>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr><th>Nombre Lógico</th><th>IP/Dominio</th><th>Puerto</th><th>Protocolo</th></tr>";
foreach ($registry->listar() as $nombre => $info) {
    echo "<tr><td>" . htmlspecialchars($nombre) . "</td><td>{$info['ip']}</td><td>{$info['puerto']}</td><td>{$info['protocolo']}</td></tr>";
}
echo "</table>";

echo "<h2>3️⃣ Búsqueda Dinámica (Lookup)</h2>";

$busqueda = $_GET['buscar'] ?? 'GoAppleSoapService';
try {
    $ref = $registry->lookup($busqueda);
    echo "<p style='color:green;'>✅ Servicio <strong>" . htmlspecialchars($busqueda) . "</strong> descubierto dinámicamente:</p>";
    echo "<p>Conectando a <code>{$ref['ip']}:{$ref['puerto']}</code> via {$ref['protocolo']}</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>4️⃣ Registro desde service_registry.json (Semana 10)</h2>";
if (file_exists($registryPath)) {
    $jsonRegistry = json_decode(file_get_contents($registryPath), true);
    echo "<p><strong>Entorno:</strong> {$jsonRegistry['environment']}</p>";
    echo "<p><strong>Nodo:</strong> {$jsonRegistry['node']}</p>";
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>Servicio</th><th>Endpoint</th><th>Protocolo</th></tr>";
    foreach ($jsonRegistry['services'] ?? [] as $svc) {
        echo "<tr><td>" . htmlspecialchars($svc['name']) . "</td><td><code>" . htmlspecialchars($svc['endpoint']) . "</code></td><td>" . htmlspecialchars($svc['protocol']) . "</td></tr>";

        echo "<h3>🔍 Prueba de Consumo (" . htmlspecialchars($svc['name']) . ")</h3>";

        if (strpos($svc['name'], 'Soap') !== false) {
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
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: text/xml; charset=UTF-8\r\nSOAPAction: http://goapple.local/ConsultarProducto\r\n",
                    'content' => $soapXml,
                    'timeout' => 10,
                ],
            ]);
            $resp = @file_get_contents($svc['endpoint'], false, $ctx);
            if ($resp) {
                echo "<p style='color:green;'>✅ Servicio respondió correctamente.</p>";
                $doc = new DOMDocument();
                $doc->loadXML($resp);
                $xp = new DOMXPath($doc);
                $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
                $xp->registerNamespace('tns', 'http://goapple.local/wsdl');
                $modelo = $xp->evaluate("string(//tns:modelo)");
                echo "<p>📱 Producto: <strong>" . htmlspecialchars($modelo) . "</strong></p>";
            } else {
                echo "<p style='color:red;'>❌ No se pudo contactar el servicio.</p>";
            }
        }
    }
    echo "</table>";
} else {
    echo "<p style='color:orange;'>⚠️ Archivo service_registry.json no encontrado en la ruta esperada.</p>";
}

echo "<hr><p>🔹 <a href='?buscar=GoAppleAuth'>Buscar GoAppleAuth</a> | ";
echo "<a href='?buscar=GoAppleSoapService'>Buscar GoAppleSoapService</a> | ";
echo "<a href='?buscar=GoAppleXmlApi'>Buscar GoAppleXmlApi</a> | ";
echo "<a href='?buscar=Inexistente'>Buscar servicio inexistente</a></p>";
