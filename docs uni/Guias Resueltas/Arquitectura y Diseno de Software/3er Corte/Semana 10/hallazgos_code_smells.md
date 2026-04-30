# ADS Semana 10 - Hallazgos de Code Smells (Top 3)

## 1) Metodo extenso en integracion
- Ubicacion: `docs uni/Guias Resueltas/Arquitectura y Diseno de Software/2do Corte/Semana 7/integracion.php`
- Hallazgo: demasiadas responsabilidades en un mismo flujo.
- Riesgo: alto costo de mantenimiento y pruebas frágiles.

## 2) Acoplamiento a endpoint externo
- Ubicacion: `docs uni/Guias Resueltas/Arquitectura y Diseno de Software/2do Corte/Semana 7/ServiceConnector.php`
- Hallazgo: dependencia directa de transporte y formato externo.
- Riesgo: cambios en proveedor impactan codigo interno.

## 3) Duplicacion de mensajes de error
- Ubicacion: capa de integracion (scripts de consumo y controlador)
- Hallazgo: patrones repetidos de manejo de error.
- Riesgo: inconsistencias funcionales y retrabajo.

## Acciones sugeridas
1. Separar responsabilidades por clase/servicio.
2. Aplicar adaptador para desacoplar API externa.
3. Centralizar manejo de errores.
