<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: james | Guía: 1 | Punto de la guía: Actividades 3 y 4 */

/**
 * Actividad 3: Delimitación de Procesos de Negocio Remotos
 * Esta clase representa la lógica crítica que se ejecutará en Ubuntu/PHP,
 * centralizando validaciones de seguridad y persistencia.
 */
class GoAppleServerLogic {
    public function validarTransaccion($datos) {
        // Lógica de servidor simulada
        return true;
    }
}

/**
 * Actividad 4: Diseño del Payload y Diagrama de Secuencia
 * Estructura de datos exacta que viajará por la red a través de sockets.
 */
class NetworkPayload {
    public string $accion;        // Ej: 'LOGIN', 'SYNC'
    public int $longitudCuerpo;   // Longitud del mensaje para evitar fragmentación
    public string $cuerpoJSON;    // Datos estructurados
    public string $delimitador = "\r\n\r\n"; // Fin del mensaje
}
