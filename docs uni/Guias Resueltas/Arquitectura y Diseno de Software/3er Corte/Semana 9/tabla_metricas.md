# ADS Semana 9 - Tabla de Metricas

## Metricas evaluadas
- LOC: lineas de codigo por clase.
- WMC: numero de metodos por clase (aprox. como peso de complejidad).
- CBO: cantidad de dependencias directas con otras clases/modulos.

| Clase/Archivo | LOC | WMC | CBO | Observacion |
|---|---:|---:|---:|---|
| `app/Core/Logger.php` | 32 | 3 | 1 | Clase simple, cohesion alta |
| `docs uni/.../Semana 7/ServiceConnector.php` | 78 | 4 | 3 | Dependencias de HTTP/JSON |
| `docs uni/.../Semana 7/IntegrationController.php` | 64 | 3 | 2 | Orquestacion de capa |
| `docs uni/.../Semana 6/app/Data/ProductoDAO.php` | 55 | 4 | 2 | Acceso a datos concentrado |
| `docs uni/.../Semana 6/app/Core/Producto.php` | 26 | 2 | 0 | Entidad limpia |

## Interpretacion
- Riesgo bajo: `Logger.php`, `Producto.php`.
- Riesgo medio: `ProductoDAO.php`, `IntegrationController.php`.
- Riesgo mayor relativo: `ServiceConnector.php` por acoplamiento con servicios externos.
