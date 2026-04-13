<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: Giorgi Julian Ordoñez | Guía: 2 | Punto de la guía: Actividad 3 */

/**
 * Actividad 3: Diseño de la Topología Lógica del MVP
 * Definición de las variables de red para evitar errores de Bind en el proyecto.
 */
class TopologiaRed {
    const SERVER_IP = '192.168.56.101'; // IP de la VM Ubuntu Server (Guest)
    const SERVER_PORT = 9000;           // Puerto exclusivo para la escucha del sistema distribuido
    const SUBNET_MASK = '255.255.255.0';
    
    public static function verificarConectividad() {
        // Simulación del comando ping mencionado en la Actividad 2
        // exec("ping -c 4 " . self::SERVER_IP, $output);
        return true; 
    }
}
