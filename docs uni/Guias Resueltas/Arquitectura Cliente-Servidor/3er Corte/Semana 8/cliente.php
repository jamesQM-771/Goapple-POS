<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h2>Cliente XML - GoApple POS (Arq. Cliente-Servidor - Semana 8)</h2>";

if (!isset($_GET['operacion'])) {
    ?>
    <p>Seleccione una operación:</p>
    <ul>
        <li><a href="?operacion=consultar_usuario">Consultar Usuario</a></li>
        <li><a href="?operacion=consultar_producto">Consultar Producto (iPhone)</a></li>
        <li><a href="?operacion=listar_productos">Listar Productos</a></li>
        <li><a href="?operacion=consultar_cliente">Consultar Cliente</a></li>
    </ul>
    <?php
    exit;
}

$operacion = $_GET['operacion'];

$params = [];
switch ($operacion) {
    case 'consultar_usuario':
        $params['usuario_id'] = $_GET['usuario_id'] ?? '1';
        break;
    case 'consultar_producto':
        $params['codigo'] = $_GET['codigo'] ?? '1';
        break;
    case 'listar_productos':
        break;
    case 'consultar_cliente':
        $params['cedula'] = $_GET['cedula'] ?? '1';
        break;
    default:
        echo "<p style='color:red;'>Operación no válida.</p>";
        exit;
}

$xml = new DOMDocument("1.0", "UTF-8");
$xml->formatOutput = true;
$root = $xml->createElement("mensaje");
$xml->appendChild($root);
$ctrl = $xml->createElement("mensajes_control");
$ctrl->appendChild($xml->createElement("id_transaccion", uniqid()));
$ctrl->appendChild($xml->createElement("timestamp", date('c')));
$ctrl->appendChild($xml->createElement("emisor", "Cliente-PHP"));
$root->appendChild($ctrl);
$req = $xml->createElement("request");
$req->appendChild($xml->createElement("operacion", $operacion));
if (!empty($params)) {
    $datosNode = $xml->createElement("datos");
    foreach ($params as $k => $v) {
        $itemNode = $xml->createElement("item");
        $itemNode->appendChild($xml->createElement("clave", $k));
        $itemNode->appendChild($xml->createElement("valor", $v));
        $datosNode->appendChild($itemNode);
    }
    $req->appendChild($datosNode);
}
$root->appendChild($req);
$xmlString = $xml->saveXML();

echo "<h3>1. XML Generado por el Cliente:</h3>";
echo "<pre style='background:#222; color:#0f0; padding:10px; overflow:auto;'>" . htmlspecialchars($xmlString) . "</pre>";

libxml_use_internal_errors(true);
$xsdPath = __DIR__ . DIRECTORY_SEPARATOR . 'protocolo.xsd';
if (!$xml->schemaValidate($xsdPath)) {
    echo "<b style='color:red;'>Error:</b> El XML generado no es válido según el XSD.<br>";
    $errores = libxml_get_errors();
    foreach ($errores as $error) {
        echo htmlspecialchars($error->message) . "<br>";
    }
    libxml_clear_errors();
    exit;
} else {
    echo "<p style='color:green;'><b>[OK] Validación XSD local exitosa.</b></p>";
}

echo "<h3>2. Enviando petición al servidor...</h3>";

$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$url = "http://" . $host . "/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%208/servidor.php";

echo "<p>Conectando a: <i>" . htmlspecialchars($url) . "</i></p>";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$responseXML = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($responseXML === false || $httpCode !== 200) {
    echo "<b style='color:red;'>Error de comunicación con el servidor. HTTP Code: $httpCode</b><br>";
    if ($curlError) {
        echo "<b>cURL Error:</b> " . htmlspecialchars($curlError) . "<br>";
    }
    if ($responseXML) {
        echo "Respuesta recibida: <br><pre>" . htmlspecialchars($responseXML) . "</pre>";
    }
    exit;
}

echo "<h3>3. Respuesta del Servidor (XML Crudo):</h3>";
echo "<pre style='background:#222; color:#0cf; padding:10px; overflow:auto;'>" . htmlspecialchars($responseXML) . "</pre>";

echo "<h3>4. Transformación XSLT de la Respuesta:</h3>";
$docRespuesta = new DOMDocument();
if ($docRespuesta->loadXML($responseXML)) {
    $xsl = new DOMDocument();
    $xsltPath = __DIR__ . DIRECTORY_SEPARATOR . 'transformacion.xslt';
    if (file_exists($xsltPath) && $xsl->load($xsltPath)) {
        $proc = new XSLTProcessor();
        $proc->importStyleSheet($xsl);
        $htmlResult = $proc->transformToXML($docRespuesta);
        echo "<div style='border: 2px solid #555; padding: 15px; background: #fff; color: #000;'>";
        echo $htmlResult;
        echo "</div>";
    } else {
        echo "<b style='color:red;'>Error:</b> Archivo transformacion.xslt no encontrado o inválido.";
    }
} else {
    echo "<b style='color:red;'>Error:</b> El XML recibido del servidor es malformado.";
}
