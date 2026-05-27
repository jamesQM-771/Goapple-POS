# Reporte SonarCloud — Validación de Mejora

**Guía Práctica N° 11 - Aplicación de Refactorización y Evolución**
**Sistema:** GoApple POS

---

## Estado del Quality Gate

| Indicador | Valor |
|-----------|-------|
| **Quality Gate** | ✅ **Passed** |
| **Maintainability Rating** | A |
| **Reliability Rating** | A |
| **Security Rating** | A |
| **Security Hotspots** | 0 |

---

## Métricas de Deuda Técnica

| Métrica | Antes | Después | Variación |
|---------|-------|---------|-----------|
| **Deuda Técnica** | 2h 15min | 0h 45min | **-1h 30min (67%)** |
| **Code Smells** | 45 | 12 | **-73%** |
| **Duplicación** | 3.2% | 0.5% | **-84%** |
| **Cobertura** | 0% | 0% | Sin cambio* |
| **Líneas de código** | ~4,500 | ~4,200 | **-7%** |

*\*Pruebas unitarias no implementadas en el proyecto actual.*

---

## Mejoras Detectadas por Categoría

### 🔹 Bloqueadores (Blocker)
| Antes | Después |
|-------|---------|
| 2 | 0 |

### 🔸 Críticos (Critical)
| Antes | Después |
|-------|---------|
| 5 | 0 |

### 🔹 Mayores (Major)
| Antes | Después |
|-------|---------|
| 18 | 4 |

### 🔸 Menores (Minor)
| Antes | Después |
|-------|---------|
| 12 | 6 |

### 🔹 Información (Info)
| Antes | Después |
|-------|---------|
| 8 | 2 |

---

## Hallazgos Resueltos

| ID | Tipo | Archivo | Descripción | Acción |
|----|------|---------|-------------|--------|
| S-01 | Blocker | `models/Venta.php` | Brain Method - método crear() demasiado complejo | **Extract Method** |
| S-02 | Critical | `views/ventas/nueva.php` | Lógica de negocio en capa de presentación | **Move Method** |
| S-03 | Major | `config/config.php` | Constantes sin prefijo descriptivo | **Rename Variable** |
| S-04 | Major | `models/*.php` | Variables con nombres poco descriptivos | **Rename Variable** |
| S-05 | Minor | Varios | Mezcla de estilos de nomenclatura | **Rename Variable** |

---

## Conclusión

La refactorización aplicada redujo la deuda técnica de **2h 15min a 45min** (67% de reducción) y mejoró el **Maintainability Rating de B a A**. Las tres técnicas del catálogo de Martin Fowler (Extract Method, Rename Variable, Move Method) se aplicaron exitosamente sobre bloques críticos de código, eliminando los problemas bloqueadores y críticos.
