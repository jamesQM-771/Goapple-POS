#!/usr/bin/env bash
set -euo pipefail

SERVER_IP="${1:-127.0.0.1}"
BASE_PATH="/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209"
WSDL_URL="http://${SERVER_IP}${BASE_PATH}/soap_server.php?wsdl"
ENDPOINT_URL="http://${SERVER_IP}${BASE_PATH}/soap_server.php"

echo "[1/2] Verificando WSDL: ${WSDL_URL}"
curl -fsS "${WSDL_URL}" > /tmp/goapple_wsdl.xml
echo "OK WSDL"

echo "[2/2] Verificando endpoint SOAP (respuesta basica)"
curl -fsS -X POST "${ENDPOINT_URL}" \
  -H "Content-Type: text/xml; charset=UTF-8" \
  -H "SOAPAction: http://goapple.local/ConsultarProducto" \
  --data-binary @- > /tmp/goapple_soap_response.xml <<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://goapple.local/wsdl">
  <soap:Header/>
  <soap:Body>
    <tns:ConsultarProductoRequest>
      <sku>APL-001</sku>
    </tns:ConsultarProductoRequest>
  </soap:Body>
</soap:Envelope>
XML

echo "OK SOAP. Archivo: /tmp/goapple_soap_response.xml"
