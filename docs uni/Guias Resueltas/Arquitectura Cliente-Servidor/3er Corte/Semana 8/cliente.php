<?php
/* Asignatura: Arquitectura Cliente-Servidor | Guía: 8 | Actividades: 1, 2, 3 y 5 */
header("Content-Type: text/html; charset=UTF-8");

echo "<h2>Cliente XML - Guía 8</h2>";

// 1. Construir el XML de petición (request)
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
$req->appendChild($xml->createElement("operacion", "consultar_usuario"));

$datosNode = $xml->createElement("datos");
$itemNode = $xml->createElement("item");
$itemNode->appendChild($xml->createElement("clave", "usuario_id"));
$itemNode->appendChild($xml->createElement("valor", "USR-999"));
$datosNode->appendChild($itemNode);

$req->appendChild($datosNode);
$root->appendChild($req);

$xmlString = $xml->saveXML();

echo "<h3>1. XML Generado por el Cliente:</h3>";
echo "<pre style='background:#222; color:#0f0; padding:10px;'>" . htmlspecialchars($xmlString) . "</pre>";

// 2. Validar estructura antes de enviar (Actividad 2)
libxml_use_internal_errors(true);
if (!$xml->schemaValidate('protocolo.xsd')) {
    echo "<b style='color:red;'>Error:</b> El XML generado no es válido según el XSD.<br>";
    $errores = libxml_get_errors();
    foreach ($errores as $error) {
        echo $error->message . "<br>";
    }
    libxml_clear_errors();
    exit;
} else {
    echo "<p style='color:green;'><b>[OK] Validación XSD local exitosa.</b></p>";
}

// 3. Enviar al Servidor usando cURL (Actividad 3)
echo "<h3>2. Enviando petición al servidor...</h3>";

// Configuración de la URL del servidor
// En Laragon, la ruta general suele ser http://goapple.test/... o http://localhost/goapple/...
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$basePath = "/goapple";
if (strpos($host, 'goapple.test') !== false) {
    $basePath = "";
}
$url = "http://" . $host . $basePath . "/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana 8/servidor.php";
$url = str_replace(' ', '%20', $url); // Asegurar URL codificada correctamente

echo "<p>Conectando a: <i>" . htmlspecialchars($url) . "</i></p>";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/xml'
]);

$responseXML = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($responseXML === false || $httpCode !== 200) {
    echo "<b style='color:red;'>Error de comunicación con el servidor. HTTP Code: $httpCode</b><br>";
    echo "Asegúrate de que Laragon esté corriendo.<br>";
    if ($responseXML) {
        echo "Respuesta recibida: <br><pre>" . htmlspecialchars($responseXML) . "</pre>";
    }
    exit;
}

echo "<h3>3. Respuesta del Servidor (XML Crudo):</h3>";
echo "<pre style='background:#222; color:#0cf; padding:10px;'>" . htmlspecialchars($responseXML) . "</pre>";

// 4. Transformación de Datos con XSLT (Actividad 5)
echo "<h3>4. Transformación XSLT de la Respuesta:</h3>";

$docRespuesta = new DOMDocument();
if ($docRespuesta->loadXML($responseXML)) {
    $xsl = new DOMDocument();
    if (file_exists('transformacion.xslt') && $xsl->load('transformacion.xslt')) {
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
?>
