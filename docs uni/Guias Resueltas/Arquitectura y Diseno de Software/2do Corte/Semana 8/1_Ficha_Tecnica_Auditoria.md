# Ficha Técnica Metodológica: Auditoría y Mantenibilidad en Arquitecturas Web

## Información General de la Investigación
- **Título de Síntesis**: "Evaluación de Calidad, Auditoría de Código y Mantenibilidad en Arquitecturas Web MVC"
- **Naturaleza del Documento**: Síntesis bibliográfica basada en 5 referencias académicas de la industria y la literatura de IEEE/SciTePress/OWASP.
- **Fecha de elaboración**: 2026
- **Palabras Clave**: Auditoría de código, Mantenibilidad, Arquitectura web, Logging, Trazabilidad, Seguridad MVC.

---

## 1. Criterios de auditoría analizados en la literatura
De la revisión de los artículos y guías de la industria, las auditorías de código en arquitecturas web (especialmente las basadas en el patrón MVC) deben enfocarse en las métricas cualitativas y de seguridad establecidas por estos autores:
- **Trazabilidad (Logging y Monitorización)**: Capacidad del sistema para registrar eventos críticos de seguridad (ej. autenticación fallida) y de negocio (mutación de datos) de forma estandarizada [2][3].
- **Acoplamiento y Cohesión**: Evaluación de qué tan independientes son las vistas, los controladores y los repositorios de datos. Se exige una barrera para que la capa de vista nunca interactúe con el DAO [4].
- **Gestión de Errores y Resiliencia**: Garantizar mediante auditoría que cualquier fallo en persistencia (ej. base de datos inaccesible) sea contenido `(try/catch)` para evitar la exposición de trazas tecnológicas sensibles al cliente final, registrándolo en silencio [3][5].
- **Cumplimiento y Calidad de Componentes**: Evaluación del software bajo criterios y métricas de deuda técnica para asegurar refactorizaciones precisas cuando el proyecto empiece a escalar [1][5].

## 2. Principales riesgos arquitectónicos observados
En los proyectos y estudios de casos de arquitecturas web monolíticas y MVPs interactivos, los académicos coinciden en estos riesgos principales:
- **Acoplamiento Directo Base de Datos - Vista**: Es muy usual que el patrón MVC temprano termine degenerando en que un Controlador integre SQL directo y llamadas a la vista, impidiendo la extensibilidad [4].
- **Carencia de Auditoría Estricta (Silent Failures)**: Las fallas se omiten o finalizan drásticamente ignorando la persistencia de los logs, volviendo la aplicación una "caja negra" ante incidentes de ciberseguridad [2][3].
- **Deuda Técnica por Escalabilidad Abrupta**: Al integrar módulos (ej. reporteador en un POS), la falta de modularización inicial estanca el desarrollo, obligando a los programadores a duplicar bloques inmensos de código para cumplir la entrega [1].

---

## 3. Referencias Bibliográficas

1. **Münch, T., & Roosmann, R. (2022).** *"Transfer, Measure and Extend Maintainability Metrics for Web Component based Applications to Achieve Higher Quality."* SciTePress. DOI: [10.5220/0011511100003318](https://doi.org/10.5220/0011511100003318)
2. **Li, X., Wang, G., Wang, C., Qin, Y., & Wang, N. (2022).** *"Software Source Code Security Audit Algorithm Supporting Incremental Checking."* IEEE. DOI: [10.1109/smartcloud55982.2022.00015](https://doi.org/10.1109/smartcloud55982.2022.00015)
3. **Open Web Application Security Project (OWASP).** *"OWASP Code Review Guide."* OWASP Foundation. Recuperado de: [https://owasp.org/www-project-code-review-guide/](https://owasp.org/www-project-code-review-guide/)
4. **Elbaz, K., et al. (2022).** *"Measuring Maintainability of Web Applications Using an Extensible MVC Architecture."* IEEE. DOI: [10.1109/icaase56196.2022.9931544](https://doi.org/10.1109/icaase56196.2022.9931544)
5. **Bosch, J., & Bengtsson, P. (2001).** *"Assessing optimal software architecture maintainability."* IEEE. DOI: [10.1109/.2001.914981](https://doi.org/10.1109/.2001.914981)
