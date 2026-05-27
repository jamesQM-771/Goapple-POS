<?php
/* Asignatura: Arquitectura Cliente-Servidor | Guía: 8 | Actividad: 3 y 4 */
header("Content-Type: application/xml; charset=UTF-8");

$xmlInput = file_get_contents('php://input');

// Si no hay datos
if (empty($xmlInput)) {
    echo generarError("FALLO", "ERR-01", "Cuerpo de la petición vacío.");
    exit;
}

$doc = new DOMDocument();
// Suprimir warnings de carga de XML malformado para manejarlos manualmente
libxml_use_internal_errors(true);
if (!$doc->loadXML($xmlInput)) {
    echo generarError("FALLO", "ERR-02", "XML malformado.");
    exit;
}

// Actividad 2 y 3: Validar contra protocolo.xsd
if (!$doc->schemaValidate('protocolo.xsd')) {
    $errores = libxml_get_errors();
    $mensajeError = "XML no cumple con el esquema: " . $errores[0]->message;
    libxml_clear_errors();
    echo generarError("FALLO", "ERR-03", $mensajeError);
    exit;
}

// Actividad 4: Uso de XPath para extracción
$xpath = new DOMXPath($doc);

// Verificar que sea un request
$nodeRequest = $xpath->query("/mensaje/request");
if ($nodeRequest->length == 0) {
    echo generarError("FALLO", "ERR-04", "El mensaje no es una solicitud (request).");
    exit;
}

// Extraer ID de transaccion
$idTransaccion = $xpath->query("/mensaje/mensajes_control/id_transaccion")->item(0)->nodeValue;

// Extraer operacion
$operacion = $xpath->query("/mensaje/request/operacion")->item(0)->nodeValue;

// Extraer parametros si existen
$items = $xpath->query("/mensaje/request/datos/item");
$parametros = [];
foreach ($items as $item) {
    $clave = $xpath->query("clave", $item)->item(0)->nodeValue;
    $valor = $xpath->query("valor", $item)->item(0)->nodeValue;
    $parametros[$clave] = $valor;
}

// Procesamiento simulado
$respuestaItems = [];
if ($operacion === "consultar_usuario") {
    $userId = isset($parametros['usuario_id']) ? $parametros['usuario_id'] : 'Desconocido';
    $respuestaItems = [
        "nombre" => "Juan Perez",
        "rol" => "Administrador",
        "usuario_id" => $userId
    ];
    $estado = "EXITO";
} else {
    echo generarError("FALLO", "ERR-05", "Operación no soportada.");
    exit;
}

// Generar respuesta
echo generarResponse($idTransaccion, $operacion, $estado, $respuestaItems);


// Funciones auxiliares
function generarError($estado, $codigo, $mensaje) {
    $xml = new DOMDocument("1.0", "UTF-8");
    $xml->formatOutput = true;
    
    $root = $xml->createElement("mensaje");
    $xml->appendChild($root);
    
    $ctrl = $xml->createElement("mensajes_control");
    $ctrl->appendChild($xml->createElement("id_transaccion", "N/A"));
    $ctrl->appendChild($xml->createElement("timestamp", date('c')));
    $ctrl->appendChild($xml->createElement("emisor", "Servidor"));
    $root->appendChild($ctrl);
    
    $error = $xml->createElement("error");
    $error->appendChild($xml->createElement("estado", $estado));
    $error->appendChild($xml->createElement("codigo", $codigo));
    $error->appendChild($xml->createElement("mensaje_error", htmlspecialchars($mensaje)));
    $root->appendChild($error);
    
    return $xml->saveXML();
}

function generarResponse($idTransaccion, $operacion, $estado, $datos) {
    $xml = new DOMDocument("1.0", "UTF-8");
    $xml->formatOutput = true;
    
    $root = $xml->createElement("mensaje");
    $xml->appendChild($root);
    
    $ctrl = $xml->createElement("mensajes_control");
    $ctrl->appendChild($xml->createElement("id_transaccion", $idTransaccion));
    $ctrl->appendChild($xml->createElement("timestamp", date('c')));
    $ctrl->appendChild($xml->createElement("emisor", "Servidor"));
    $root->appendChild($ctrl);
    
    $resp = $xml->createElement("response");
    $resp->appendChild($xml->createElement("operacion", $operacion));
    $resp->appendChild($xml->createElement("estado", $estado));
    
    if (!empty($datos)) {
        $datosNode = $xml->createElement("datos");
        foreach ($datos as $k => $v) {
            $itemNode = $xml->createElement("item");
            $itemNode->appendChild($xml->createElement("clave", $k));
            $itemNode->appendChild($xml->createElement("valor", htmlspecialchars($v)));
            $datosNode->appendChild($itemNode);
        }
        $resp->appendChild($datosNode);
    }
    
    $root->appendChild($resp);
    
    return $xml->saveXML();
}
