<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: james | Guía: 3 | Punto de la guía: Actividad 2 */

/**
 * Actividad 2: Creación del Descriptor de Socket y Bind
 * Habilitación de la extensión PHP-Sockets (Actividad 1 simulada por entorno).
 */
$host = '127.0.0.1'; // Debe coincidir con la topología
$port = 9000;

// Creación del descriptor
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("Error en socket_create(): " . socket_strerror(socket_last_error()) . "\n");
}

// Enlace (Bind) a la IP y puerto
$bind = socket_bind($socket, $host, $port);
if ($bind === false) {
    die("Error en socket_bind(): " . socket_strerror(socket_last_error($socket)) . "\n");
}

echo "Socket enlazado correctamente a $host:$port\n";
// Se dejará abierto temporalmente o se cerrará para evitar bloquear el puerto
socket_close($socket);
