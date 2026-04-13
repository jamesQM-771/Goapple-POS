<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: Giorgi Julian Ordoñez | Guía: 4 | Punto de la guía: Actividad 1, 2 y 3 */

$host = '127.0.0.1';
$port = 9000;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);

/**
 * Actividad 1: Algoritmos de Escucha (Listen & Accept)
 */
socket_listen($socket, 5);
echo "Servidor en espera de conexiones en el puerto $port...\n";

// Actividad 1: Aceptar conexión
// $clientSocket = socket_accept($socket); // Comentado para evitar bloqueos continuos
// if ($clientSocket) {
/*
    /**
     * Actividad 2: Intercambio de Payload (Lectura/Escritura)
     * /
    $input = socket_read($clientSocket, 1024);
    echo "Payload recibido: $input\n";
    
    $response = "Respuesta del servidor - Payload procesado\n";
    socket_write($clientSocket, $response, strlen($response));
    
    /**
     * Actividad 3: Protocolo de Cierre (Socket Close)
     * /
    socket_close($clientSocket);
*/
// }

// Cierre del servidor
socket_close($socket);
