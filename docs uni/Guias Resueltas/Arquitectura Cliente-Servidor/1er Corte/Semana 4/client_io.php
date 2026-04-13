<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: james | Guía: 4 | Punto de la guía: Actividad 2 y 3 */

/**
 * Actividad 2: Intercambio de Payload (Lectura/Escritura) del lado del Cliente
 */
$host = '127.0.0.1';
$port = 9000;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
// @socket_connect($socket, $host, $port); // Comentado para evitar errores si no hay servidor vivo

$payload = json_encode(['accion' => 'SALUDO', 'datos' => 'Hola Servidor']) . "\r\n";
// socket_write($socket, $payload, strlen($payload));

// Leer respuesta
// $respuesta = socket_read($socket, 1024);
// echo "Respuesta recibida: " . $respuesta . "\n";

/**
 * Actividad 3: Protocolo de Cierre (Socket Close)
 */
socket_close($socket);
