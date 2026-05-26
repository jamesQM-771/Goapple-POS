<?php
header("Content-Type: application/xml; charset=UTF-8");

require_once __DIR__ . '/../../../../../config/database.php';

$xmlInput = file_get_contents('php://input');

if (empty($xmlInput)) {
    echo generarError("FALLO", "ERR-01", "Cuerpo de la petición vacío.");
    exit;
}

$doc = new DOMDocument();
libxml_use_internal_errors(true);
if (!$doc->loadXML($xmlInput)) {
    echo generarError("FALLO", "ERR-02", "XML malformado.");
    exit;
}

$xsdPath = __DIR__ . DIRECTORY_SEPARATOR . 'protocolo.xsd';
if (!$doc->schemaValidate($xsdPath)) {
    $errores = libxml_get_errors();
    $mensajeError = "XML no cumple con el esquema: " . $errores[0]->message;
    libxml_clear_errors();
    echo generarError("FALLO", "ERR-03", $mensajeError);
    exit;
}

$xpath = new DOMXPath($doc);

$nodeRequest = $xpath->query("/mensaje/request");
if ($nodeRequest->length == 0) {
    echo generarError("FALLO", "ERR-04", "El mensaje no es una solicitud (request).");
    exit;
}

$idTransaccion = $xpath->query("/mensaje/mensajes_control/id_transaccion")->item(0)->nodeValue;
$operacion = $xpath->query("/mensaje/request/operacion")->item(0)->nodeValue;

$items = $xpath->query("/mensaje/request/datos/item");
$parametros = [];
foreach ($items as $item) {
    $clave = $xpath->query("clave", $item)->item(0)->nodeValue;
    $valor = $xpath->query("valor", $item)->item(0)->nodeValue;
    $parametros[$clave] = $valor;
}

try {
    $db = Database::getInstance()->getConnection();

    switch ($operacion) {
        case 'consultar_usuario':
            $userId = $parametros['usuario_id'] ?? '';
            $stmt = $db->prepare("SELECT id, nombre, email, rol, telefono, estado FROM usuarios WHERE id = ? OR email = ? LIMIT 1");
            $stmt->execute([$userId, $userId]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                echo generarError("FALLO", "ERR-05", "Usuario no encontrado.");
                exit;
            }

            echo generarResponse($idTransaccion, $operacion, "EXITO", [
                "id" => (string)$usuario['id'],
                "nombre" => $usuario['nombre'],
                "email" => $usuario['email'],
                "rol" => $usuario['rol'],
                "telefono" => (string)$usuario['telefono'],
                "estado" => $usuario['estado']
            ]);
            break;

        case 'consultar_producto':
            $codigo = $parametros['codigo'] ?? '';
            $stmt = $db->prepare("
                SELECT i.*, p.nombre AS proveedor_nombre
                FROM iphones i
                LEFT JOIN proveedores p ON i.proveedor_id = p.id
                WHERE i.imei = ? OR i.id = ?
                LIMIT 1
            ");
            $stmt->execute([$codigo, $codigo]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                echo generarError("FALLO", "ERR-05", "Producto no encontrado.");
                exit;
            }

            echo generarResponse($idTransaccion, $operacion, "EXITO", [
                "id" => (string)$producto['id'],
                "modelo" => $producto['modelo'],
                "capacidad" => $producto['capacidad'],
                "color" => $producto['color'],
                "imei" => $producto['imei'],
                "precio_venta" => number_format((float)$producto['precio_venta'], 2, '.', ''),
                "estado" => $producto['estado'],
                "proveedor" => $producto['proveedor_nombre'] ?? 'N/A'
            ]);
            break;

        case 'listar_productos':
            $stmt = $db->query("
                SELECT i.id, i.modelo, i.capacidad, i.color, i.imei, i.precio_venta, i.estado
                FROM iphones i
                ORDER BY i.id DESC
                LIMIT 50
            ");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $items = [];
            foreach ($productos as $p) {
                $items[] = [
                    "id" => (string)$p['id'],
                    "modelo" => $p['modelo'],
                    "capacidad" => $p['capacidad'],
                    "color" => $p['color'],
                    "imei" => $p['imei'],
                    "precio_venta" => number_format((float)$p['precio_venta'], 2, '.', ''),
                    "estado" => $p['estado']
                ];
            }

            echo generarResponseArray($idTransaccion, $operacion, "EXITO", $items);
            break;

        case 'consultar_cliente':
            $cedula = $parametros['cedula'] ?? '';
            $stmt = $db->prepare("SELECT * FROM clientes WHERE cedula = ? OR id = ? LIMIT 1");
            $stmt->execute([$cedula, $cedula]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                echo generarError("FALLO", "ERR-05", "Cliente no encontrado.");
                exit;
            }

            echo generarResponse($idTransaccion, $operacion, "EXITO", [
                "id" => (string)$cliente['id'],
                "nombre" => $cliente['nombre'],
                "cedula" => $cliente['cedula'],
                "telefono" => (string)$cliente['telefono'],
                "email" => (string)$cliente['email'],
                "estado" => $cliente['estado'],
                "limite_credito" => number_format((float)$cliente['limite_credito'], 2, '.', ''),
                "credito_disponible" => number_format((float)$cliente['credito_disponible'], 2, '.', '')
            ]);
            break;

        default:
            echo generarError("FALLO", "ERR-06", "Operación no soportada: $operacion.");
            exit;
    }
} catch (Exception $e) {
    echo generarError("FALLO", "ERR-99", "Error interno del servidor.");
}

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
            $itemNode->appendChild($xml->createElement("valor", htmlspecialchars((string)$v)));
            $datosNode->appendChild($itemNode);
        }
        $resp->appendChild($datosNode);
    }
    $root->appendChild($resp);
    return $xml->saveXML();
}

function generarResponseArray($idTransaccion, $operacion, $estado, $items) {
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
    $datosNode = $xml->createElement("datos");
    foreach ($items as $index => $item) {
        $itemGroup = $xml->createElement("item");
        $itemGroup->appendChild($xml->createElement("clave", "producto_" . $index));
        $valorXml = "<item>";
        foreach ($item as $k => $v) {
            $valorXml .= "<$k>" . htmlspecialchars((string)$v) . "</$k>";
        }
        $valorXml .= "</item>";
        $itemGroup->appendChild($xml->createElement("valor", $valorXml));
        $datosNode->appendChild($itemGroup);
    }
    $resp->appendChild($datosNode);
    $root->appendChild($resp);
    return $xml->saveXML();
}
