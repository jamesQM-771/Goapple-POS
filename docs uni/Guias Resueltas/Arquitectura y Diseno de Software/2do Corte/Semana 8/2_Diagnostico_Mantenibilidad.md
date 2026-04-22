# Diagnóstico de Mantenibilidad del Proyecto: Sistema GoApple POS

De acuerdo con la Unidad 2 y los principios de Arquitectura y Diseño de Software, a continuación se responde a los puntos de auditoría interna sobre el código evaluado hasta la fecha.

## 1. Transparencia: ¿Es fácil identificar dónde termina la lógica de negocio y dónde empieza la persistencia?
En la arquitectura actual estructurada para el marco MVC (con directorios como `/controllers`, `/models` y `/config`), es relativamente sencillo identificar los límites:
- **Lógica de negocio y control**: Reside en la capa de controladores (ej. `IntegrationController.php`, `AuthController.php`, etc.) los cuales verifican qué flujo debe seguirse, sanitan los inputs y delegan las tareas de datos a los modelos.
- **Capa de Persistencia**: Empieza estrictamente en la carpeta `models` (por ejemplo `Usuario.php` o las implementaciones que heredan las conexiones BD).
Sin embargo, **aún existe oportunidad de separar más la capa de persistencia**. El modelo actualmente se acopla mediante inyección explícita del recurso estático o PDO, de esta manera el uso de *interfaces* o de un patrón "Repository" realzaría la transparencia para en un futuro migrar de motor sin cambiar ni una línea de la lógica del Modelo.

## 2. Gestión de Excepciones: ¿Qué sucede con la arquitectura si la base de datos MySQL no responde?
Si analizamos el flujo actual documentado en el sistema (específicamente en la clase `Database` del archivo `/config/database.php`), el comportamiento ante la falla de MySQL es el siguiente:
1. El objeto `PDO` intenta levantar la conexión de forma segura en bloques `try...catch(PDOException $e)`.
2. Al fallar, la excepción es capturada internamente, evitando exponer las credenciales y el trazado de la conexión.
3. Se invoca el comando `error_log()` registrando la falla técnica silenciosamente en los logs de servidor local.
4. Seguidamente, se finaliza el proceso por completo mediante `die("Error de conexión a la base de datos. Por favor contacte al administrador.");`.

**Impacto Arquitectónico**: Este flujo de error es de **corte abrupto o parada restrictiva**. Si la BD falla, toda la presentación y API se detiene, previniendo lecturas de estados corruptos, pero sacrificando la resiliencia en la interfaz (la pantalla final del cliente se muestra vacía solo con el mensaje de error de texto plano, en vez de renderizar una página 503 decorada o buscar datos desde caché secundaria).

## 3. Escalabilidad: Modificación Inmediata, Agregar un módulo "Reportes"
Si se requiere agregar un nuevo módulo de "Reportes" de forma inmediata, la arquitectura actual respondería de manera eficiente gracias al enfoque MVC, aunque requeriría impacto estructural por las dependencias:
- **Vista y Controlador**: Crearíamos `ReporteController` y una vista especializada en `views/reportes`. Esto es de adopción inmediata sin que afecte al resto de código modularizado.
- **Modelos y Datos (Acoplamiento de Consultas)**: El problema principal radicaría en que los "Reportes" suelen cruzar datos de muchos submódulos (Clientes, Ventas, Usuarios). Si no se crea una abstracción de analítica e integramos la lógica de Joins nativos SQL, acabaríamos repitiendo lógica ya escrita en varios Modelos (rompiendo DRY).
- **Sobrecargas Sistémicas**: Un uso inmediato de Reportes ejecutaría peticiones masivas en bases conectadas mediante PDO único, esto implicaría que con concurrencia elevada se generen bloqueos. Sería necesario contemplar separación de read-and-write bases de datos a largo plazo. En resumen, la integración inicial sería natural en estructura pero demandaría precaución a nivel persistencia para proteger el rendimiento general de los clientes que están operando el POS concurridamente.
