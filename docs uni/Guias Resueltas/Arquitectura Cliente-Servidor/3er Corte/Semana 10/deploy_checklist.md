# ACS Semana 10 - Checklist de Despliegue Virtualizado

## 1. Infraestructura virtualizada
- [ ] Ubuntu Server instalado y actualizado.
- [ ] Apache + PHP habilitados.
- [ ] Puerto 80 habilitado en firewall.
- [ ] Topologia validada entre nodo cliente y servidor.

## 2. Publicacion de servicios
- [ ] Endpoint SOAP publicado (`soap_server.php`).
- [ ] WSDL accesible desde cliente externo.
- [ ] Servicio de sockets (si aplica) con daemon de escucha activo.
- [ ] Prueba de conectividad con `curl` o navegador.

## 3. Descubrimiento dinamico (estilo UDDI)
- [ ] Registro de servicio actualizado (`service_registry.json`).
- [ ] Cliente consume endpoint desde registro, no hardcoded.

## 4. Validacion final
- [ ] Request/response exitoso en red real.
- [ ] Evidencia de error controlado (fault).
- [ ] Log de prueba final generado.
