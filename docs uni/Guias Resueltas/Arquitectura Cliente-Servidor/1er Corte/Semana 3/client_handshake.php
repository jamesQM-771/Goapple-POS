<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: james | Guía: 3 | Punto de la guía: Actividad 3 */

/**
 * Actividad 3: Simulación de Handshake (Apretón de Manos)
 * Primera petición de conexión desde el cliente para verificar el canal de transporte.
 */
$host = '127.0.0.1';
$port = 9000;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("Error al crear socket cliente: " . socket_strerror(socket_last_error()) . "\n");
}

// Simular el apretón de manos
$result = @socket_connect($socket, $host, $port);
if ($result === false) {
    echo "Fallo el Handshake. El servidor no está escuchando.\n";
} else {
    echo "Handshake exitoso. Conexión establecida con el servidor.\n";
}

socket_close($socket);
