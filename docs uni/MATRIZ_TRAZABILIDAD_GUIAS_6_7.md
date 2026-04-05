# Matriz de Trazabilidad - Guias 6 y 7

## Guia 6 - Registry

- Requisito: modulo de directorio con bind/lookup.
  Evidencia: `docs uni/registry.php`.
- Requisito: service registration al iniciar.
  Evidencia: `docs uni/PagosRemotos.php` (`ServicioPagosRemoto::iniciar`).
- Requisito: cliente sin IP hardcodeada.
  Evidencia: `docs uni/ClienteDinamico.php` (lookup por nombre logico).

## Guia 7 - Callbacks + DCOM + Optimizacion

- Requisito: callback reference desde stub.
  Evidencia: `docs uni/Guia7_Callback_DCOM.php` (`StubClienteRemoto::conectarYRegistrarCallback`).
- Requisito: notificacion asincrona servidor -> cliente.
  Evidencia: `ServidorEventos::emitirEvento` + `ClienteCallbackEndpoint::onNotify`.
- Requisito: simulacion DCOM (UUID/registro).
  Evidencia: `ServidorEventos::registrarComponente`.
- Requisito: prueba valor vs referencia.
  Evidencia: `ParametroBenchmark::enviarPorValor` y `enviarPorReferencia`.
