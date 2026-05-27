<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>🔌 Middleware y Comunicación Remota — GoApple POS</h1>";
echo "<p>Demostración práctica de patrones de comunicación remota (Callbacks, DCOM, Paso de Parámetros) consumiendo servicios reales.</p>";

require_once __DIR__ . '/../../../../../config/database.php';

/**
 * Actividad 1: Remote Callbacks
 * Registra callbacks y notifica eventos usando el servicio SOAP real
 */
class ServidorCallbacks {
    private $clientesSuscritos = [];

    public function registrarCallbackCliente($referenciaCliente) {
        $this->clientesSuscritos[] = $referenciaCliente;
        echo "<p style='color:green;'>✅ Cliente registrado para Callback: <code>" . htmlspecialchars($referenciaCliente) . "</code></p>";
    }

    public function notificarEvento($mensaje) {
        echo "<h3>📢 Notificaciones Asíncronas:</h3>";
        foreach ($this->clientesSuscritos as $idx => $callbackRef) {
            echo "<p>→ Notificando a <strong>$callbackRef</strong>: $mensaje</p>";
        }
    }
}

/**
 * Actividad 2: Middleware de Servicios
 * Versión real que consume el servicio SOAP de GoApple
 */
class GestorServiciosRemotos {

    private $endpoint;

    public function __construct() {
        $this->endpoint = "http://localhost/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20y%20Diseno%20de%20Software/3er%20Corte/Semana%209/soap_server.php";
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

/**
 * Actividad 3: Paso de Parámetros (Valor vs Referencia)
 * Compara payload completo vs consulta por ID
 */
function pasoPorValor($sku) {
    echo "<p>🔹 <strong>Paso por Valor:</strong> Se envía el SKU '$sku' en el cuerpo del mensaje SOAP completo. " .
         "Se transmite todo el estado del producto. Latencia mayor, pero el cliente tiene todos los datos.</p>";

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

        echo "<p>📦 Producto recibido: <strong>$modelo</strong> — $" . number_format((float)$precio, 0, ',', '.') . "</p>";
    }
}

function pasoPorReferencia($id) {
    echo "<p>🔸 <strong>Paso por Referencia:</strong> Se transmite solo el ID '$id'. " .
         "Menor ancho de banda, pero requiere consulta adicional para obtener los datos completos.</p>";

    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT modelo, precio_venta FROM iphones WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            echo "<p>📦 Datos recuperados localmente (referencia): <strong>" . htmlspecialchars($producto['modelo']) . "</strong> — $" .
                 number_format($producto['precio_venta'], 0, ',', '.') . "</p>";
        } else {
            echo "<p>❌ Producto no encontrado con ID: $id</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr><h2>📋 Ejecución de Actividades</h2>";

echo "<h3>🔹 Actividad 1: Remote Callbacks</h3>";
$server = new ServidorCallbacks();
$server->registrarCallbackCliente("SOAP/GoApple/10.0.0.5");
$server->registrarCallbackCliente("REST/GoApple/192.168.1.10");
$server->notificarEvento("¡Nuevo iPhone 17 Pro disponible en inventario!");

echo "<h3>🔹 Actividad 2: Middleware SOAP (anteriormente DCOM simulado)</h3>";
$gestor = new GestorServiciosRemotos();
$sku = $_GET['sku'] ?? '1';
$respuesta = $gestor->consultarProducto($sku);

echo "<p>Consultando SKU: <strong>$sku</strong></p>";
if ($respuesta) {
    echo "<p style='color:green;'>✅ Servicio SOAP respondió correctamente.</p>";
    echo "<pre style='background:#222; color:#0cf; padding:10px; max-height:200px; overflow:auto;'>" . htmlspecialchars($respuesta) . "</pre>";

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
