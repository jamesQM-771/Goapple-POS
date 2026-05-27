<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../../config/database.php';

function soapFaultXml(string $code, string $message): string
{
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>{$code}</faultcode>
      <faultstring>{$message}</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
XML;
}

function soapSuccessXml(array $data): string
{
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Body>
    <tns:ConsultarProductoResponse>
      <id>{$data['id']}</id>
      <modelo>{$data['modelo']}</modelo>
      <capacidad>{$data['capacidad']}</capacidad>
      <color>{$data['color']}</color>
      <imei>{$data['imei']}</imei>
      <precio>{$data['precio']}</precio>
      <stock>{$data['stock']}</stock>
      <condicion>{$data['condicion']}</condicion>
      <estado>{$data['estado']}</estado>
    </tns:ConsultarProductoResponse>
  </soap:Body>
</soap:Envelope>
XML;
}

function handleSoapRequest(string $raw): string
{
    if (trim($raw) === '') {
        return soapFaultXml('Client', 'Peticion vacia.');
    }

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    if (!$doc->loadXML($raw)) {
        return soapFaultXml('Client', 'XML invalido.');
    }

    $xp = new DOMXPath($doc);
    $xp->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $sku = trim((string) $xp->evaluate('string(//sku)'));

    if ($sku === '') {
        return soapFaultXml('Client', 'Parametro sku requerido.');
    }

    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT i.*, p.nombre AS proveedor_nombre
            FROM iphones i
            LEFT JOIN proveedores p ON i.proveedor_id = p.id
            WHERE i.imei = ? OR i.id = ?
            LIMIT 1
        ");
        $stmt->execute([$sku, $sku]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            return soapFaultXml('Server', 'Producto no encontrado con SKU: ' . $sku);
        }

        return soapSuccessXml([
            'id' => (string)$producto['id'],
            'modelo' => htmlspecialchars($producto['modelo']),
            'capacidad' => htmlspecialchars($producto['capacidad']),
            'color' => htmlspecialchars($producto['color']),
            'imei' => htmlspecialchars($producto['imei']),
            'precio' => number_format((float)$producto['precio_venta'], 2, '.', ''),
            'stock' => ($producto['estado'] === 'disponible') ? '1' : '0',
            'condicion' => $producto['condicion'],
            'estado' => $producto['estado'],
        ]);
    } catch (Exception $e) {
        return soapFaultXml('Server', 'Error interno al consultar producto.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $wsdl = __DIR__ . DIRECTORY_SEPARATOR . 'servicio.wsdl';
    if (isset($_GET['wsdl']) || (isset($_SERVER['QUERY_STRING']) && stripos($_SERVER['QUERY_STRING'], 'wsdl') !== false)) {
        header('Content-Type: text/xml; charset=UTF-8');
        readfile($wsdl);
        exit;
    }
    echo "SOAP server activo. Use POST SOAP o ?wsdl";
    exit;
}

$raw = file_get_contents('php://input') ?: '';
header('Content-Type: text/xml; charset=UTF-8');
echo handleSoapRequest($raw);
