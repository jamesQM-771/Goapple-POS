<?php

declare(strict_types=1);

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
      <nombre>{$data['nombre']}</nombre>
      <precio>{$data['precio']}</precio>
      <stock>{$data['stock']}</stock>
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

    $fakeDb = [
        'APL-001' => ['nombre' => 'iPhone 16 Pro', 'precio' => '1200.00', 'stock' => '14'],
        'APL-002' => ['nombre' => 'iPhone 15', 'precio' => '900.00', 'stock' => '22'],
    ];

    if (!isset($fakeDb[$sku])) {
        return soapFaultXml('Server', 'Producto no encontrado.');
    }

    return soapSuccessXml($fakeDb[$sku]);
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
