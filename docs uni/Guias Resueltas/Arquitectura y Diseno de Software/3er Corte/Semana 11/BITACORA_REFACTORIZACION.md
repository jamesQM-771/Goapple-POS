# Bitácora de Técnicas de Refactorización

**Guía Práctica N° 11 - Aplicación de Refactorización y Evolución**
**Sistema:** GoApple POS
**Fecha:** 19/05/2026

---

## Técnica 1: Extract Method

### Descripción
División de métodos grandes en funciones más pequeñas y cohesivas para mejorar la legibilidad y mantenibilidad.

### Aplicación en GoApple POS

**Antes:** El método `crear()` en `models/Venta.php` contenía toda la lógica de inserción de venta, detalle, actualización de stock y creación de crédito en un solo bloque monolítico de ~100 líneas.

**Después:** Se extrajo la lógica de creación de crédito a un método separado `crearCredito()` y la generación de número de crédito a `generarNumeroCredito()`.

**Archivos afectados:**
- `models/Venta.php`

**Código Antes (fragmento):**
```php
// Lógica de crédito mezclada con la venta
if ($tipo_venta === 'credito') {
    $query_credito = "INSERT INTO creditos (...) VALUES (...)";
    // 40+ líneas de lógica de crédito dentro del método crear()
}
```

**Código Después (fragmento):**
```php
public function crearCredito($ventaId, $clienteId, $total) {
    $numero = $this->generarNumeroCredito();
    $query = "INSERT INTO creditos (numero_credito, venta_id, ...) VALUES (?, ?, ...)";
    // Lógica de crédito encapsulada en su propio método
}

private function generarNumeroCredito() {
    // Lógica de generación de consecutivo
}
```

**Beneficio:** El método `crear()` se redujo de ~100 a ~50 líneas. Cada método ahora tiene una única responsabilidad.

---

## Técnica 2: Rename Variable/Method

### Descripción
Normalización de nombres de variables, métodos y constantes para cumplir con el estándar PSR-12 y mejorar la legibilidad del código.

### Aplicación en GoApple POS

**Antes:** Nombres inconsistentes, mezcla de español/inglés, y estilos de nomenclatura variados:
- Variables con nombres crípticos como `$qry`, `$res`, `$stmt1`
- Constantes sin prefijo descriptivo
- Mezcla de `camelCase` y `snake_case` en nombres de métodos

**Después:** Se estandarizaron los nombres:
- `$qry` → `$query`
- `$res` → `$result`
- Se unificó el uso de `snake_case` para métodos públicos
- Se agregaron constantes con nombres descriptivos como `TASA_INTERES_DEFAULT`, `COMISION_DEFAULT_PCT`

**Archivos afectados:**
- `config/config.php` (nuevas constantes)
- `models/*.php` (normalización de variables)

**Beneficio:** El código es más legible y sigue un estándar consistente, facilitando el mantenimiento futuro.

---

## Técnica 3: Move Method

### Descripción
Reubicación de lógica de negocio que estaba en la capa incorrecta (vistas o controladores) hacia los modelos correspondientes.

### Aplicación en GoApple POS

**Antes:** La lógica de creación de crédito estaba implementada directamente en `views/ventas/nueva.php`, mezclando presentación con lógica de negocio. También había lógica de validación de stock en las vistas.

**Después:** Se movió toda la lógica de negocio a los modelos:
- Creación de créditos → `models/Venta.php` (método `crearCredito()`)
- Validación de disponibilidad → `models/iPhone.php`
- Cálculo de intereses → `models/Credito.php`

**Archivos afectados:**
- `models/Venta.php` (nuevos métodos de negocio)
- `views/ventas/nueva.php` (solo llamadas a métodos del modelo)
- `models/Credito.php` (cálculos financieros)

**Código Antes (en vista):**
```php
// En views/ventas/nueva.php - lógica de negocio en la vista
$interes = $total * 0.035;
$cuota = ($total + $interes) / 12;
// INSERT directo a tabla creditos
```

**Código Después (en modelo):**
```php
// En models/Venta.php - lógica encapsulada
public function crearCredito($ventaId, $clienteId, $total) {
    $interes = $total * (TASA_INTERES_DEFAULT / 100);
    // ... inserción centralizada
}
```

**Beneficio:** Separación clara de responsabilidades siguiendo el patrón MVC. Las vistas solo se encargan de presentar datos, mientras que los modelos manejan la lógica de negocio.

---

## Resumen de Mejoras

| Técnica | Archivos Modificados | Líneas Afectadas | Impacto |
|---------|---------------------|-------------------|---------|
| Extract Method | `models/Venta.php` | ~80 líneas | Métodos más pequeños y cohesivos |
| Rename Variable/Method | `config/config.php`, varios modelos | ~30 variables | Código más legible y estándar |
| Move Method | `models/Venta.php`, `views/ventas/nueva.php` | ~60 líneas | MVC correcto, vista limpia |
