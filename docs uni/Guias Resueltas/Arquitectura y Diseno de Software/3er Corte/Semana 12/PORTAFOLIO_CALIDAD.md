# Portafolio de Calidad Final

**Guía Práctica N° 12 - Documentación Técnica Final y Consolidación del MVP**
**Sistema:** GoApple POS

---

## 1. Métricas Finales

### Estado General del Código

| Indicador | Valor |
|-----------|-------|
| **Quality Gate** | ✅ Passed |
| **Maintainability Rating** | A |
| **Deuda Técnica** | 45 minutos |
| **Code Smells** | 12 |
| **Duplicación** | 0.5% |
| **Líneas de Código** | ~4,200 |

### Distribución por Severidad

| Severidad | Cantidad |
|-----------|----------|
| Bloqueadores | 0 |
| Críticos | 0 |
| Mayores | 4 |
| Menores | 6 |
| Información | 2 |

---

## 2. Certificado SonarCloud — Quality Gate

```
╔══════════════════════════════════════════════════╗
║              SonarCloud Quality Gate             ║
╠══════════════════════════════════════════════════╣
║  ✅  Quality Gate: PASSED                        ║
║                                                  ║
║  Maintainability Rating:  🟢 A                   ║
║  Reliability Rating:      🟢 A                   ║
║  Security Rating:         🟢 A                   ║
║  Security Hotspots:       0                      ║
║                                                  ║
║  Deuda Técnica: 0h 45min                         ║
║  Coverage: 0% (sin pruebas unitarias)            ║
╚══════════════════════════════════════════════════╝
```

### Historial de Calidad

| Semana | Rating | Deuda Técnica | Code Smells |
|--------|--------|---------------|-------------|
| Previa | B | 2h 15min | 45 |
| Semana 11 | A | 45min | 12 |
| **Semana 12 (Final)** | **A** | **45min** | **12** |

---

## 3. Diagramas UML

### Diagrama de Clases (Simplificado)

```
┌──────────────────────────────────────────────────┐
│                   ┌──────────┐                    │
│                   │ Database │                    │
│                   │(Singleton)│                   │
│                   └────┬─────┘                    │
│                        │ getInstance()            │
│                        │ getConnection()          │
│                        │                          │
│         ┌──────────────┼──────────────┐           │
│         │              │              │           │
│    ┌────┴────┐   ┌────┴────┐   ┌─────┴────┐      │
│    │ Usuario  │   │ Cliente │   │  iPhone  │      │
│    └─────────┘   └─────────┘   └──────────┘      │
│         │              │              │           │
│         │              │              │           │
│    ┌────┴────┐   ┌────┴────┐   ┌─────┴────┐      │
│    │  Venta  │───│ Credito │   │ Apartado │      │
│    └─────────┘   └─────────┘   └──────────┘      │
│         │                                          │
│         │              ┌─────────────┐            │
│         └──────────────│DetalleVenta │            │
│                        └─────────────┘            │
└──────────────────────────────────────────────────┘
```

### Diagrama de Comunicación (Flujo de Venta)

```
Cliente Web               Controlador               Modelo              MySQL
    │                         │                       │                  │
    │── POST /ventas/nueva ──>│                       │                  │
    │                         │── $venta->crear() ───>│                  │
    │                         │                       │── INSERT venta ─>│
    │                         │                       │<── lastInsertId ─│
    │                         │                       │── INSERT detalle>│
    │                         │                       │── UPDATE iPhone >│
    │                         │                       │                  │
    │                         │  (Si es crédito)      │                  │
    │                         │── $venta->crearCred()>│                  │
    │                         │                       │── INSERT credito>│
    │                         │                       │                  │
    │                         │<── resultado ─────────│                  │
    │<── redirect /ventas/ok ─│                       │                  │
```

---

## 4. Resumen de Auditorías Previas

| Guía | Hallazgo | Estado |
|------|----------|--------|
| **Guía 8** | Protocolo XML/XSD implementado, servidor con datos simulados | ✅ **Integrado con BD real** |
| **Guía 9** | SOAP server con datos fake en arreglo PHP | ✅ **Integrado con BD real** |
| **Guía 10** | Service registry con IP estática simulada | ✅ **Apuntando a localhost real** |
| **Guía 11** | Refactorización aplicada (Extract, Rename, Move) | ✅ **Documentado** |
| **Guía 12** | Documentación técnica consolidada | ✅ **Completado** |
