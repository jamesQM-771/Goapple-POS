# Guía Práctica N° 7: Optimización y Middleware de Microsoft (Callbacks y DCOM)

**Asignatura:** Arquitectura y Diseño del Software  
**Semestre:** 2026-1  
**Estudiante:** [Tu Nombre]  
**Fecha:** [Fecha de entrega]  

---

## 1. Introducción

Esta guía práctica implementa conceptos avanzados de middleware en sistemas distribuidos, enfocándose en:

- **Actividad 1:** Implementación de Remote Callbacks para notificaciones asíncronas.
- **Actividad 2:** Simulación de Automatización y DCOM (Distributed Component Object Model).
- **Actividad 3:** Optimización del Paso de Parámetros (por valor vs. por referencia).

El sistema demuestra el uso de callbacks remotos y comparación de estrategias de paso de parámetros en entornos distribuidos.

---

## 2. Arquitectura del Sistema

### Componentes Implementados

1. **Servidor de Callbacks (`ServidorCallbacks`)**: Gestiona suscripciones de clientes y notificaciones.
2. **Gestor DCOM Simulado (`GestorDCOMSimulado`)**: Simula instanciación remota de objetos.
3. **Funciones de Paso de Parámetros**: Demuestra diferencias entre paso por valor y referencia.

### Flujo de Callbacks

```
Cliente → Registra callback en servidor → Servidor almacena referencia
Servidor → Evento ocurre → Notifica a todos los clientes suscritos
Cliente → Recibe notificación asíncrona
```

---

## 3. Implementación Detallada

### 3.1 Servidor de Callbacks

```php
<?php
class ServidorCallbacks {
    private $clientesSuscritos = [];

    public function registrarCallbackCliente($referenciaCliente) {
        $this->clientesSuscritos[] = $referenciaCliente;
        echo "Cliente registrado para Callback.\n";
    }

    public function notificarEvento($mensaje) {
        foreach ($this->clientesSuscritos as $idx => $callbackRef) {
            echo "Notificando asíncronamente al cliente $idx: $mensaje\n";
            // Ejecutar la funcion de callback del cliente remoto
        }
    }
}
?>
```

**Funcionalidades:**
- Almacena referencias de clientes suscritos.
- Permite registro de callbacks remotos.
- Notifica eventos a todos los clientes de forma asíncrona.

### 3.2 Simulación DCOM

```php
class GestorDCOMSimulado {
    public function instanciarRemoto($clsid) {
        echo "DCOM SCM: Instanciando objeto remoto con CLSID $clsid en máquina destino.\n";
    }
}
```

**Funcionalidades:**
- Simula el Service Control Manager (SCM) de DCOM.
- Instancia objetos remotos usando CLSID (Class Identifier).

### 3.3 Optimización de Paso de Parámetros

```php
function pasoPorValor($objetoSerializado) {
    echo "Paso por Valor: Se transmite todo el estado. Latencia mayor, menor acoplamiento.\n";
}

function pasoPorReferencia($punteroRed) {
    echo "Paso por Referencia: Se transmite solo ID $punteroRed. Menos BW, pero requiere comunicación continua.\n";
}
```

**Comparación:**

| Aspecto | Paso por Valor | Paso por Referencia |
|---------|----------------|-------------------|
| **Ancho de Banda** | Alto (todo el objeto) | Bajo (solo identificador) |
| **Latencia** | Mayor | Menor |
| **Acoplamiento** | Bajo | Alto |
| **Concurrencia** | Mejor | Requiere sincronización |

### 3.4 Ejecución del Sistema

```php
// Flujos
$server = new ServidorCallbacks();
$server->registrarCallbackCliente("Ref/Cliente/10.0.0.5");
$server->notificarEvento("Stock de GoApple actualizado!");
```

**Salida esperada:**
```
Cliente registrado para Callback.
Notificando asíncronamente al cliente 0: Stock de GoApple actualizado!
```

---

## 4. Análisis de Middleware y DCOM

### 4.1 Remote Callbacks

**Ventajas:**
- Comunicación asíncrona eficiente.
- Desacoplamiento entre emisor y receptor.
- Escalabilidad para múltiples clientes.

**Desventajas:**
- Complejidad en gestión de referencias remotas.
- Riesgo de referencias huérfanas.
- Dificultad en debugging.

### 4.2 DCOM (Distributed Component Object Model)

**Características:**
- Modelo de objetos distribuidos de Microsoft.
- Basado en COM (Component Object Model).
- Soporte para instanciación remota transparente.

**Ventajas:**
- Integración nativa con Windows.
- Transparencia de ubicación.
- Seguridad integrada (autenticación, autorización).

**Limitaciones:**
- Plataforma específica (Windows).
- Complejidad de configuración.
- Reemplazado por WCF/.NET Remoting en versiones modernas.

### 4.3 Optimización de Parámetros

**Estrategias de Optimización:**
1. **Paso por Referencia:** Para objetos grandes, pasar solo identificadores.
2. **Lazy Loading:** Cargar datos solo cuando se necesitan.
3. **Compresión:** Comprimir datos antes de transmisión.
4. **Caching:** Almacenar en caché resultados frecuentes.

---

## 5. Conclusiones

Esta implementación demuestra conceptos fundamentales de middleware distribuido:

1. **Callbacks Remotos:** Permiten notificaciones asíncronas eficientes en sistemas distribuidos.
2. **DCOM:** Proporciona un framework robusto para objetos distribuidos en entornos Windows.
3. **Optimización:** El paso por referencia reduce significativamente el uso de ancho de banda.

La guía establece las bases para sistemas distribuidos optimizados, preparando el terreno para tecnologías más modernas como WCF y servicios web.

---

## 6. Referencias

- Microsoft DCOM Documentation
- COM/DCOM Specifications
- Distributed Systems Design Patterns
- PHP Manual - Object Serialization</content>
<parameter name="filePath">c:\laragon\www\goapple\docs uni\Guias Resueltas\Arquitectura y Diseno de Software\3er Corte\Semana 7\DOCUMENTACION_SEMANA7.md