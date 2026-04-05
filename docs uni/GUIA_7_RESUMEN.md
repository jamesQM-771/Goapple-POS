# Guia 7 - Callbacks, DCOM y Optimizacion de Parametros

Fecha objetivo de guia: 2026-04-20

## Objetivo cubierto

Implementar callbacks remotos asincronos, simular conceptos DCOM (UUID/registro de componente) y medir impacto de envio por valor vs referencia.

## Actividad 1 - Remote Callbacks

Evidencia en codigo:

- `docs uni/Guia7_Callback_DCOM.php`
- `StubClienteRemoto::conectarYRegistrarCallback()` envia callback reference.
- `ServidorEventos` guarda callbacks activos y ejecuta notificaciones bidireccionales.

## Actividad 2 - Simulacion DCOM

Evidencia en codigo:

- `ServidorEventos::registrarComponente()` genera UUID y simula registro de componente remoto.
- Comparativo conceptual:

| Aspecto | RMI Registry | DCOM (Windows) |
|---|---|---|
| Identificador | Nombre logico | CLSID/UUID |
| Resolucion | Lookup por nombre | SCM + Registry Editor |
| Ciclo de vida | Ligero, app-level | Integrado al sistema |
| Interoperabilidad | Java-centric clasico | Ecosistema Microsoft |

## Actividad 3 - Optimizacion valor vs referencia

Evidencia en codigo:

- `ParametroBenchmark::enviarPorValor()`
- `ParametroBenchmark::enviarPorReferencia()`

Resultado esperado:

- Para payload grande, por referencia transmite menos bytes y mejora latencia de red.

## Como ejecutar evidencia

```bash
php "docs uni/Guia7_Callback_DCOM.php"
```
