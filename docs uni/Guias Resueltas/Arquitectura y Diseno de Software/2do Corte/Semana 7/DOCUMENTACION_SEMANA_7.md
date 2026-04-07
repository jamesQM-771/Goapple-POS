# Documentación de Entrega - Semana 7
**Materia:** Arquitectura y Diseño de Software
**Tema:** Integración de Web Services y Diagramación Estructural

## 1. Contenido de la Carpeta
- `ServiceConnector.php`: Script con la lógica de consumo del Web Service (REST/JSON) usando cURL. (Entregable 1).
- `IntegrationController.php`: Controlador que orquestra la sincronización entre el servicio y el modelo.
- `integracion.php`: Vista de usuario para la prueba de estrés y visualización en tiempo real.
- `iPhoneModel.php`: Modelo de negocio actualizado con soporte para persistencia masiva (`upsertFromService`).

## 2. Diagrama de Clases UML (Diseño Estructural)
Este diagrama representa la arquitectura implementada, identificando el patrón **Singleton** en la base de datos y la capa de interoperabilidad.

```mermaid
classDiagram
    class Database {
        -static instance
        -conn
        +getInstance() Database
        +getConnection() PDO
    }
    
    class iPhoneModel {
        -conn
        -table
        +crear(datos)
        +actualizar(id, datos)
        +upsertFromService(datos)
    }
    
    class ServiceConnector {
        -apiUrl
        +fetchExternalProducts() array
        -handleError(ch)
    }
    
    class IntegrationController {
        +syncAction()
    }
    
    Database "1" <-- "1" iPhoneModel : utiliza (Singleton)
    iPhoneModel "1" <.. "1" IntegrationController : relación de dependencia
    ServiceConnector "1" <.. "1" IntegrationController : relación de dependencia
    ServiceConnector ..> "API Externa" : consume (REST/JSON)
```

## 3. Manejo de Errores
Se ha implementado el manejo de los siguientes escenarios en `ServiceConnector.php`:
- **Timeouts:** Límite de 10 segundos para la conexión.
- **Códigos 404/500:** Verificación del `http_code` tras la ejecución de cURL.
- **Errores de Red:** Captura de mensajes de error mediante `curl_error()`.

## 4. Sustentación de Integración
Para visualizar la sincronización:
1. Navegar a la vista `integracion.php` dentro del sistema POS.
2. Hacer clic en "Iniciar Sincronización".
3. El sistema consumirá `https://dummyjson.com/products/category/smartphones`, mapeará los datos a objetos de negocio y los persistirá en la tabla `iphones` de MySQL.
