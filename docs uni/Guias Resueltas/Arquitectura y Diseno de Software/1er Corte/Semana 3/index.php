<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: Giorgi Julian Ordoñez | Guía: 3 */
echo "<h1>Proyecto Iniciado</h1>";
include '../Semana 2/app/Data/conexion.php';

// Simulación de datos provenientes de una API Externa
$json_externo = '{"servicio": "Auth_Global", "status": "Conectado", "latencia": "20ms"}';
$datos_api = json_decode($json_externo);
echo "<h2>Estado de Comunicación Externa</h2>";
echo "Servicio: " . $datos_api->servicio . "<br>";
echo "Estado: " . $datos_api->status . "<br>";
echo "Respuesta: Comunicación estandarizada correctamente mediante JSON.<br>";
?>