<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>📤 Cliente I/O — Intercambio de Payload con Servidor</h1>";
echo "<p>Simula un cliente que envía payloads al servidor y lee las respuestas con datos reales.</p>";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = "http://{$host}/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%208/servidor.php";

$accion = $_GET['accion'] ?? 'consultar_producto';
$parametros = [];

switch ($accion) {
    case 'consultar_producto':
        $parametros['codigo'] = $_GET['codigo'] ?? '1';
        break;
    case 'consultar_usuario':
        $parametros['usuario_id'] = $_GET['usuario_id'] ?? '1';
        break;
    case 'consultar_cliente':
        $parametros['cedula'] = $_GET['cedula'] ?? '1';
        break;
    case 'listar_productos':
        break;
}

echo "<h2>1️⃣ Construcción del Payload</h2>";

$xml = new DOMDocument("1.0", "UTF-8");
$xml->formatOutput = true;
$root = $xml->createElement("mensaje");
$xml->appendChild($root);
$ctrl = $xml->createElement("mensajes_control");
$ctrl->appendChild($xml->createElement("id_transaccion", uniqid()));
$ctrl->appendChild($xml->createElement("timestamp", date('c')));
$ctrl->appendChild($xml->createElement("emisor", "Cliente-I/O"));
$root->appendChild($ctrl);
$req = $xml->createElement("request");
$req->appendChild($xml->createElement("operacion", $accion));
if (!empty($parametros)) {
    $datosNode = $xml->createElement("datos");
    foreach ($parametros as $k => $v) {
        $itemNode = $xml->createElement("item");
        $itemNode->appendChild($xml->createElement("clave", $k));
        $itemNode->appendChild($xml->createElement("valor", $v));
        $datosNode->appendChild($itemNode);
    }
    $req->appendChild($datosNode);
}
$root->appendChild($req);
$xmlString = $xml->saveXML();

echo "<p><strong>Payload XML generado ({$accion}):</strong></p>";
echo "<pre style='background:#222; color:#0f0; padding:10px;'>" . htmlspecialchars($xmlString) . "</pre>";
echo "<p><strong>Longitud:</strong> " . strlen($xmlString) . " bytes</p>";

echo "<h2>2️⃣ Envío al Servidor (POST HTTP)</h2>";
echo "<p>Conectando a: <code>" . htmlspecialchars($baseUrl) . "</code></p>";

$ch = curl_init($baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$responseXML = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($responseXML === false || $httpCode !== 200) {
    echo "<p style='color:red;'>❌ Error de comunicación. HTTP $httpCode</p>";
    if ($responseXML) {
        echo "<pre>" . htmlspecialchars($responseXML) . "</pre>";
    }
    exit;
}

echo "<p style='color:green;'>✅ Payload enviado. Respuesta recibida (HTTP $httpCode).</p>";

echo "<h2>3️⃣ Lectura de Respuesta</h2>";
echo "<pre style='background:#222; color:#0cf; padding:10px;'>" . htmlspecialchars($responseXML) . "</pre>";

echo "<h2>4️⃣ Protocolo de Cierre</h2>";
echo "<p style='color:green;'>✅ Socket cerrado. Canal de comunicación liberado.</p>";

echo "<hr><p>";
echo "<a href='?accion=consultar_producto&codigo=1'>Consultar Producto ID=1</a> | ";
echo "<a href='?accion=consultar_usuario&usuario_id=1'>Consultar Usuario ID=1</a> | ";
echo "<a href='?accion=consultar_cliente&cedula=1'>Consultar Cliente</a> | ";
echo "<a href='?accion=listar_productos'>Listar Productos</a>";
echo "</p>";
