# ADS Semana 9 - Catalogo de Desarmonias (Code Smells)

## 1) Acoplamiento externo en `ServiceConnector.php`
- Tipo: High Coupling.
- Evidencia: el modulo integra transporte HTTP, parseo de respuesta y logica de transformacion.
- Impacto: cambios en proveedor externo obligan cambios directos en la clase.

## 2) Metodo con demasiada responsabilidad en `integracion.php`
- Tipo: Long Method / God Script.
- Evidencia: flujo de entrada, validacion, llamada a servicio y salida en un mismo script.
- Impacto: pruebas unitarias mas dificiles y mantenimiento costoso.

## 3) Duplication de mensajes de error en capa de integracion
- Tipo: Duplicated Code.
- Evidencia: patrones repetidos de manejo de error en scripts de consumo.
- Impacto: inconsistencia funcional y mayor costo de cambio.
