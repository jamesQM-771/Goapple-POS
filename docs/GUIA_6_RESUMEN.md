# Guia 6 - Sistema de Nombrado y Registro (Registry)

Fecha objetivo de guia: 2026-04-06

## Objetivo cubierto

Implementar un servicio de directorio (Registry) que vincule nombre logico de servicio con referencia de red para permitir busqueda dinamica desde cliente.

## Actividad 1 - Registry (bind/lookup)

Evidencia en codigo:

- `docs uni/registry.php`
- Metodos implementados: `bind()`, `lookup()`, `all()`

## Actividad 2 - Service Registration

Evidencia en codigo:

- `docs uni/PagosRemotos.php`
- `ServicioPagosRemoto::iniciar()` realiza auto-registro en Registry.
- Si el servicio existe, `bind()` actualiza referencia (interoperabilidad).

## Actividad 3 - Naming System en cliente

Evidencia en codigo:

- `docs uni/ClienteDinamico.php`
- Cliente busca por nombre logico (`ServicioPagos`) y elimina IP hardcodeada.

## Como ejecutar evidencia

```bash
php "docs uni/PagosRemotos.php"
php "docs uni/ClienteDinamico.php"
```

## Resultado esperado

- Registro inicial del servicio.
- Actualizacion de referencia al reiniciar servicio.
- Cliente resuelve conexion por lookup, no por IP fija.
