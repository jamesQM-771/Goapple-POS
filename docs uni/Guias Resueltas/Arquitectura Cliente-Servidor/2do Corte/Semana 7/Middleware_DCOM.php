<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: james | Guía: 7 | Punto de la guía: Actividad 1, 2 y 3 */

/**
 * Actividad 1: Implementación de Remote Callbacks
 * Referencia de cliente almacenada en servidor para notificación asíncrona.
 */
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

/**
 * Actividad 2: Simulación de Automatización y DCOM
 * Comparación simulada de UUIDs para invocación remota.
 */
class GestorDCOMSimulado {
    public function instanciarRemoto($clsid) {
        echo "DCOM SCM: Instanciando objeto remoto con CLSID $clsid en máquina destino.\n";
    }
}

/**
 * Actividad 3: Optimización del Paso de Parámetros
 * Mostrar diferencia entre Valor y Referencia en Payload.
 */
function pasoPorValor($objetoSerializado) {
    echo "Paso por Valor: Se transmite todo el estado. Latencia mayor, menor acoplamiento.\n";
}

function pasoPorReferencia($punteroRed) {
    echo "Paso por Referencia: Se transmite solo ID $punteroRed. Menos BW, pero requiere comunicación continua.\n";
}

// Flujos
$server = new ServidorCallbacks();
$server->registrarCallbackCliente("Ref/Cliente/10.0.0.5");
$server->notificarEvento("Stock de GoApple actualizado!");

$dcom = new GestorDCOMSimulado();
$dcom->instanciarRemoto("{12345678-1234-1234-1234-1234567890AB}");
