<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>🌐 Topología de Red — GoApple POS</h1>";
echo "<p>Verificación de conectividad con los servicios del sistema.</p>";

require_once __DIR__ . '/../../../../../config/database.php';

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

echo "<h2>📡 Configuración de Red</h2>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr><td><strong>Servidor local</strong></td><td>{$host}</td></tr>";
echo "<tr><td><strong>URL Base</strong></td><td>http://{$host}/goapple</td></tr>";
echo "<tr><td><strong>API XML (Semana 8)</strong></td><td>http://{$host}/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%208/servidor.php</td></tr>";
echo "<tr><td><strong>API SOAP (Semana 9)</strong></td><td>http://{$host}/goapple/docs%20uni/Guias%20Resueltas/Arquitectura%20Cliente-Servidor/3er%20Corte/Semana%209/soap_server.php</td></tr>";
echo "</table>";

echo "<h2>🔌 Estado de Conexión a Servicios</h2>";

function probarEndpoint($url, $nombre) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $icono = ($httpCode > 0 && $httpCode < 500) ? '✅' : '❌';
    echo "<tr><td>$nombre</td><td><code>" . htmlspecialchars($url) . "</code></td><td>$icono (HTTP $httpCode)</td></tr>";
}

echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr><th>Servicio</th><th>URL</th><th>Estado</th></tr>";
probarEndpoint("http://{$host}/goapple/login.php", "Login");
probarEndpoint("http://{$host}/goapple/config/config.php", "Config (no directo)");
echo "</table>";

echo "<h2>📊 Estadísticas de Red desde la BD</h2>";
try {
    $db = Database::getInstance()->getConnection();

    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>Indicador</th><th>Valor</th></tr>";
    echo "<tr><td>Usuarios activos</td><td>" . $db->query("SELECT COUNT(*) FROM usuarios WHERE estado='activo'")->fetchColumn() . "</td></tr>";
    echo "<tr><td>Clientes registrados</td><td>" . $db->query("SELECT COUNT(*) FROM clientes")->fetchColumn() . "</td></tr>";
    echo "<tr><td>Productos disponibles</td><td>" . $db->query("SELECT COUNT(*) FROM iphones WHERE estado='disponible'")->fetchColumn() . "</td></tr>";
    echo "<tr><td>Ventas realizadas</td><td>" . $db->query("SELECT COUNT(*) FROM ventas")->fetchColumn() . "</td></tr>";
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
