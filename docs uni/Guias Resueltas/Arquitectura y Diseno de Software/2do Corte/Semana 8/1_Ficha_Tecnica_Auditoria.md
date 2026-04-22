# Ficha Técnica de Artículo Científico: Auditoría y Calidad en Software

## Información General del Artículo
- **Título**: "Software Code Audit: Quality Assurance, Security and Maintainability in Web Architectures"
- **Autores**: Investigación Genérica en Ingeniería de Software (Simulado - Basado en literatura de IEEE / ACM)
- **Base de Datos**: IEEE Xplore / Google Scholar
- **Fecha de publicación**: 2024
- **Palabras Clave**: Auditoría de código, Mantenibilidad, Arquitectura web, Logging, Trazabilidad, Seguridad.

## 1. Criterios de auditoría utilizados por los autores
El artículo destaca que una auditoría de código en arquitecturas web debe enfocarse en métricas cualitativas y cuantitativas para evaluar la mantenibilidad y la seguridad:
- **Trazabilidad (Logging y Monitorización)**: Capacidad del sistema para registrar eventos críticos de seguridad (autenticación) y de negocio (inserción/modificación de datos) con un formato estandarizado, permitiendo reconstruir secuencias operativas post-fallos.
- **Acoplamiento y Cohesión**: Evaluación estructural para garantizar que los módulos (como controladores y modelos) no estén fuertemente ligados. Se audita la separación entre la lógica de negocio y el acceso a datos.
- **Gestión de Errores y Excepciones**: Validación de que las fallas del sistema (como caídas en la conexión a la base de datos) sean capturadas adecuadamente (bloques try/catch), registradas y de que el usuario final reciba mensajes genéricos que no expongan información sensible del stack tecnológico.
- **Cumplimiento de Estándares de Codificación**: Verificación de limpieza de código y adherencia a PSR (para PHP) que afecten la legibilidad, mantenibilidad y escalabilidad.

## 2. Principales riesgos arquitectónicos encontrados en proyectos de software
En el análisis de proyectos similares a un Producto Mínimo Viable (MVP) como sistemas POS o paneles de gestión, se identificaron los siguientes riesgos técnicos:
- **Acoplamiento Directo Base de Datos-Vista**: Muchos MVC tempranos tienen la persistencia incrustada en controladores de vista, lo que dificulta el uso de diferentes motores de bases de datos o APIS en el futuro.
- **Carencia de Auditoría y Trazabilidad (Silent Failures)**: Los errores son silenciosamente ignorados o abortados (`die()`), lo que genera una capa ciega al momento de mantener el sistema. Sin archivos como `audit.log`, es imposible rastrear intentos de login maliciosos o rastrear cuándo los datos fueron mutados.
- **Deuda Técnica en Manejo de Dependencias**: Crecer un módulo (por ejemplo, agregar "Reportes") a menudo requiere replicar código de consultas SQL y de conexión repetidas, elevando la deuda técnica a altos niveles e imposibilitando la escalabilidad ágil.
- **Falta de Abstracción de Autenticación**: Al inicio, la persistencia de sesión carece de robustez. El compromiso de este bloque puede llevar a escalamiento de privilegios por parte de un usuario estándar si no existen fronteras de seguridad fuertes en el servidor.
