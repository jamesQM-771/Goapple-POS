<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>🔌 Middleware y Comunicación Remota — GoApple POS (Arq. Cliente-Servidor)</h1>";
echo "<p>Demostración de patrones de middleware: Callbacks, Servicios Remotos y Paso de Parámetros con datos reales.</p>";

require_once __DIR__ . '/../../../../../config/database.php';

class ServidorCallbacks {
    private $clientesSuscritos = [];

    public function registrarCallbackCliente($referenciaCliente) {
        $this->clientesSuscritos[] = $referenciaCliente;
        echo "<p style='color:green;'>✅ Cliente registrado para Callback: <code>" . htmlspecialchars($referenciaCliente) . "</code></p>";
    }

    public function notificarEvento($mensaje) {
        echo "<h3>📢 Notificaciones Asíncronas a " . count($this->clientesSuscritos) . " clientes:</h3>";
        foreach ($this->clientesSuscritos as $idx => $callbackRef) {
            echo "<p>→ Notificando a <strong>" . htmlspecialchars($callbackRef) . "</strong>: $mensaje</p>";
        }
    }
}

class GestorServiciosRemotos {

    private $endpoint;

    public function __construct() {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $this->endpoint = "http://{$host}/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php";
    }

    public function consultarProducto($sku) {
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
                'timeout' => 15,
            ],
        ]);

        $response = @file_get_contents($this->endpoint, false, $ctx);
        return $response;
    }
}

function pasoPorValor($sku) {
    echo "<p>🔹 <strong>Paso por Valor:</strong> Se envía el SKU '$sku' completo en el payload SOAP.</p>";

    $gestor = new GestorServiciosRemotos();
    $respuesta = $gestor->consultarProducto($sku);

    if ($respuesta) {
        $doc = new DOMDocument();
        $doc->loadXML($respuesta);
        $xp = new DOMXPath($doc);
        $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xp->registerNamespace('tns', 'http://goapple.local/wsdl');

        $modelo = $xp->evaluate("string(//tns:modelo)");
        $precio = $xp->evaluate("string(//tns:precio)");

        echo "<p>📦 Producto completo recibido: <strong>$modelo</strong> — \$" . number_format((float)$precio, 0, ',', '.') . "</p>";
    }
}

function pasoPorReferencia($id) {
    echo "<p>🔸 <strong>Paso por Referencia:</strong> Se transmite solo el ID '$id'. Menos ancho de banda.</p>";

    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT modelo, precio_venta FROM iphones WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            echo "<p>📦 Datos recuperados por referencia: <strong>" . htmlspecialchars($producto['modelo']) . "</strong> — \$" .
                 number_format($producto['precio_venta'], 0, ',', '.') . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr><h2>📋 Ejecución de Actividades</h2>";

echo "<h3>🔹 Actividad 1: Remote Callbacks</h3>";
$server = new ServidorCallbacks();
$server->registrarCallbackCliente("SOAP/GoApple/" . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
$server->registrarCallbackCliente("XML/GoApple/192.168.1.10");
$server->notificarEvento("¡Nuevo inventario de iPhones disponible!");

echo "<h3>🔹 Actividad 2: Middleware de Servicios Remotos</h3>";
$gestor = new GestorServiciosRemotos();
$sku = $_GET['sku'] ?? '1';
$respuesta = $gestor->consultarProducto($sku);

echo "<p>Consultando SKU: <strong>$sku</strong></p>";
if ($respuesta) {
    echo "<p style='color:green;'>✅ Servicio SOAP respondió correctamente.</p>";

    $doc = new DOMDocument();
    $doc->loadXML($respuesta);
    $xp = new DOMXPath($doc);
    $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xp->registerNamespace('tns', 'http://goapple.local/wsdl');

    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    foreach (['id', 'modelo', 'capacidad', 'color', 'imei', 'precio', 'stock', 'condicion', 'estado'] as $campo) {
        $valor = $xp->evaluate("string(//tns:$campo)");
        echo "<tr><td>" . ucfirst($campo) . "</td><td>" . htmlspecialchars($valor) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ Error al consultar servicio SOAP.</p>";
}

echo "<h3>🔹 Actividad 3: Paso de Parámetros (Valor vs Referencia)</h3>";
pasoPorValor($sku);
pasoPorReferencia($sku);

echo "<hr><p><a href='?sku=1'>SKU=1</a> | <a href='?sku=2'>SKU=2</a> | <a href='?sku=3'>SKU=3</a></p>";
