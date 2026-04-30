# ACS Semana 9 - SOAP WSDL UDDI

## Entregables tecnicos implementados
- `servicio.wsdl`: contrato del servicio.
- `soap_server.php`: servidor SOAP con manejo de `SOAP Fault`.
- `soap_client.php`: cliente consumidor del endpoint.
- `uddi_registry.json`: directorio simulado para descubrimiento dinamico.

## Prueba sugerida
1. Publicar carpeta en Apache/Laragon.
2. Abrir `.../soap_server.php?wsdl` para validar contrato.
3. Ejecutar `soap_client.php`.
4. Verificar archivos `soap_request.xml` y `soap_response.xml`.
