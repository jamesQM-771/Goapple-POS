<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: james | Guía: 5 | Punto de la guía: Actividad 1, 2 y 3 */

/**
 * Actividad 1: Definición de Interfaces y Clases Serializables
 * Esta clase debe ser idéntica en cliente y servidor para el Marshaling.
 */
class Producto {
    public $id;
    public $nombre;
    public $precio;

    public function __construct($id, $nombre, $precio) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
    }
}

/**
 * Actividad 2: Implementación del Stub (Marshaling de Objetos)
 * Envío de objetos transformados en bytes.
 */
class ClientStub {
    public function enviarObjeto(Producto $producto) {
        // Convierte un objeto vivo en bytes
        $payloadMarshaled = serialize($producto);
        echo "Objeto Serializado (Stub): " . $payloadMarshaled . "\n";
        // Aquí iría el socket_write($socket, $payloadMarshaled);
    }
}

/**
 * Actividad 3: Reconstrucción en el Servidor (Unmarshaling)
 */
class ServerUnmarshaling {
    public function recibirObjeto($payloadBytes) {
        // Vuelve a convertir los bytes en un objeto vivo
        $producto = unserialize($payloadBytes);
        echo "Objeto Reconstruido en Servidor: " . var_export($producto, true) . "\n";
    }
}

// Emulación del flujo:
$stub = new ClientStub();
$prod = new Producto(1, "GoApple Pro", 999.99);
$stub->enviarObjeto($prod);

$bytesSimulados = serialize($prod);
$server = new ServerUnmarshaling();
$server->recibirObjeto($bytesSimulados);

