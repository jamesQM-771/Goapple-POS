<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: james | Guía: 2 */
echo "<h1>🚀 GoApple POS — Proyecto Iniciado</h1>";
echo "<p>Esta página verifica que la arquitectura de capas esté funcionando correctamente.</p>";
echo "<hr><h3>Estado de Conexión:</h3>";
include 'app/Data/conexion.php';

echo "<hr><h3>Estadísticas del Sistema:</h3>";
try {
    require_once __DIR__ . '/../../../../../config/database.php';
    $db = Database::getInstance()->getConnection();

    $stats = [
        'usuarios' => $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
        'clientes' => $db->query("SELECT COUNT(*) FROM clientes")->fetchColumn(),
        'ventas' => $db->query("SELECT COUNT(*) FROM ventas")->fetchColumn(),
        'creditos' => $db->query("SELECT COUNT(*) FROM creditos")->fetchColumn(),
        'apartados' => $db->query("SELECT COUNT(*) FROM apartados")->fetchColumn(),
    ];

    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>Módulo</th><th>Registros</th></tr>";
    foreach ($stats as $modulo => $total) {
        echo "<tr><td>" . ucfirst($modulo) . "</td><td>" . $total . "</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error al obtener estadísticas: " . htmlspecialchars($e->getMessage()) . "</p>";
}
