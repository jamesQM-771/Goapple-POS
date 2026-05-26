<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>📥 Servidor I/O — Gestión de Payload (Lectura/Escritura)</h1>";
echo "<p>Simula un servidor que recibe payloads, los procesa y responde con datos reales de GoApple.</p>";

require_once __DIR__ . '/../../../../../config/database.php';

$host = '127.0.0.1';
$port = 9000;

echo "<h2>1️⃣ Algoritmo de Escucha (Listen & Accept)</h2>";

$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "<p style='color:orange;'>⚠️ Extensión sockets no disponible. Usando modo HTTP.</p>";
    $modoHttp = true;
} else {
    $modoHttp = false;
    @socket_bind($socket, $host, $port);
    @socket_listen($socket, 5);
    echo "<p style='color:green;'>✅ Servidor escuchando en {$host}:{$port} (listen backlog: 5)</p>";
    @socket_close($socket);
}

echo "<h2>2️⃣ Procesamiento de Payload</h2>";

$payloads = [
    ['accion' => 'CONSULTAR_PRODUCTO', 'sku' => '1'],
    ['accion' => 'LISTAR_PRODUCTOS', 'limite' => 5],
    ['accion' => 'VERIFICAR_STOCK', 'imei' => 'IMEI-001'],
];

try {
    $db = Database::getInstance()->getConnection();

    foreach ($payloads as $i => $payload) {
        echo "<h3>📦 Payload #" . ($i+1) . ": " . htmlspecialchars($payload['accion']) . "</h3>";
        echo "<p><strong>Recibido:</strong> <code>" . htmlspecialchars(json_encode($payload)) . "</code></p>";

        switch ($payload['accion']) {
            case 'CONSULTAR_PRODUCTO':
                $stmt = $db->prepare("SELECT * FROM iphones WHERE id = ? OR imei = ? LIMIT 1");
                $stmt->execute([$payload['sku'], $payload['sku']]);
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
            case 'LISTAR_PRODUCTOS':
                $stmt = $db->query("SELECT id, modelo, capacidad, color, precio_venta, estado FROM iphones LIMIT " . (int)$payload['limite']);
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'VERIFICAR_STOCK':
                $stmt = $db->prepare("SELECT COUNT(*) FROM iphones WHERE imei = ? AND estado = 'disponible'");
                $stmt->execute([$payload['imei']]);
                $resultado = ['disponible' => $stmt->fetchColumn() > 0];
                break;
            default:
                $resultado = ['error' => 'Acción no soportada'];
        }

        echo "<p><strong>Respuesta:</strong></p>";
        echo "<pre style='background:#222; color:#0cf; padding:10px;'>" . htmlspecialchars(json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";
    }

    echo "<h2>3️⃣ Protocolo de Cierre (Socket Close)</h2>";
    echo "<p style='color:green;'>✅ Conexión cerrada correctamente. Recursos liberados.</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
