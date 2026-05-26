<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>🔌 Servidor Socket — GoApple POS (Bind & Listen)</h1>";
echo "<p>Simula un servidor de sockets que escucha peticiones y sirve datos reales de GoApple.</p>";

require_once __DIR__ . '/../../../../../config/database.php';

$host = '127.0.0.1';
$port = 9000;

echo "<h2>📡 Configuración del Socket</h2>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr><td><strong>IP de escucha</strong></td><td>{$host}</td></tr>";
echo "<tr><td><strong>Puerto</strong></td><td>{$port}</td></tr>";
echo "<tr><td><strong>Protocolo</strong></td><td>TCP</td></tr>";
echo "</table>";

$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "<p style='color:orange;'>⚠️ Extensión sockets no disponible. Usando HTTP como canal alternativo.</p>";
    $modoHttp = true;
} else {
    $modoHttp = false;
    $bind = @socket_bind($socket, $host, $port);
    if ($bind === false) {
        echo "<p style='color:orange;'>⚠️ No se pudo enlazar al puerto {$port} (probablemente en uso). Usando HTTP.</p>";
        socket_close($socket);
        $modoHttp = true;
    } else {
        echo "<p style='color:green;'>✅ Socket enlazado correctamente a {$host}:{$port}</p>";
        socket_listen($socket, 5);
        echo "<p style='color:green;'>✅ Servidor en escucha (listen) activo.</p>";
        socket_close($socket);
    }
}

echo "<h2>📦 Datos Servidos (desde BD real)</h2>";

try {
    $db = Database::getInstance()->getConnection();

    $tipo = $_GET['tipo'] ?? 'productos';

    switch ($tipo) {
        case 'usuarios':
            $stmt = $db->query("SELECT id, nombre, email, rol FROM usuarios WHERE estado='activo' LIMIT 5");
            break;
        case 'productos':
            $stmt = $db->query("SELECT id, modelo, capacidad, color, imei, precio_venta, estado FROM iphones ORDER BY id DESC LIMIT 5");
            break;
        case 'ventas':
            $stmt = $db->query("SELECT v.id, v.numero_venta, v.total, v.tipo_venta, c.nombre AS cliente
                FROM ventas v JOIN clientes c ON v.cliente_id = c.id ORDER BY v.id DESC LIMIT 5");
            break;
        default:
            $stmt = $db->query("SELECT id, modelo, precio_venta FROM iphones LIMIT 5");
    }

    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p><strong>Tipo de datos:</strong> {$tipo}</p>";
    echo "<pre style='background:#222; color:#0f0; padding:10px;'>";
    echo htmlspecialchars(json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "</pre>";

    echo "<p><a href='?tipo=usuarios'>Usuarios</a> | <a href='?tipo=productos'>Productos</a> | <a href='?tipo=ventas'>Ventas</a></p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
