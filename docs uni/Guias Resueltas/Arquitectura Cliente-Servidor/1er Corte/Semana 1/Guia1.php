<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>🖥️ Servidor GoApple — Procesos de Negocio Remotos</h1>";
echo "<p>Demostración de procesos de negocio remotos con datos reales de GoApple POS.</p>";

require_once __DIR__ . '/../../../../../config/database.php';

class GoAppleServerLogic {

    private $db;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function validarTransaccion($datos) {
        if (empty($datos['tipo']) || empty($datos['valor'])) {
            return ['valido' => false, 'error' => 'Datos incompletos'];
        }

        switch ($datos['tipo']) {
            case 'usuario':
                $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND estado = 'activo'");
                $stmt->execute([$datos['valor']]);
                return ['valido' => $stmt->rowCount() > 0, 'tipo' => 'usuario'];

            case 'producto':
                $stmt = $this->db->prepare("SELECT id FROM iphones WHERE imei = ? AND estado = 'disponible'");
                $stmt->execute([$datos['valor']]);
                return ['valido' => $stmt->rowCount() > 0, 'tipo' => 'producto'];

            case 'cliente':
                $stmt = $this->db->prepare("SELECT id, limite_credito, credito_disponible FROM clientes WHERE cedula = ? AND estado = 'activo'");
                $stmt->execute([$datos['valor']]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
                return [
                    'valido' => $cliente ? true : false,
                    'tipo' => 'cliente',
                    'limite_credito' => $cliente['limite_credito'] ?? 0,
                    'credito_disponible' => $cliente['credito_disponible'] ?? 0
                ];

            default:
                return ['valido' => false, 'error' => 'Tipo de transacción no soportado'];
        }
    }

    public function obtenerPayload($tipo) {
        switch ($tipo) {
            case 'usuarios':
                return $this->db->query("SELECT id, nombre, email, rol FROM usuarios LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            case 'productos':
                return $this->db->query("SELECT id, modelo, capacidad, precio_venta, estado FROM iphones LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            case 'ventas':
                return $this->db->query("SELECT v.id, v.numero_venta, v.total, v.tipo_venta, c.nombre AS cliente
                    FROM ventas v JOIN clientes c ON v.cliente_id = c.id ORDER BY v.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            default:
                return [];
        }
    }
}

class NetworkPayload {
    public string $accion;
    public int $longitudCuerpo;
    public string $cuerpoJSON;
    public string $delimitador = "\r\n\r\n";
}

$server = new GoAppleServerLogic();

echo "<h2>📋 Validación de Transacciones</h2>";
$pruebas = [
    ['tipo' => 'usuario', 'valor' => 'admin@goapple.com'],
    ['tipo' => 'producto', 'valor' => 'IMEI-001'],
    ['tipo' => 'cliente', 'valor' => '1234567890'],
    ['tipo' => 'desconocido', 'valor' => 'test'],
];

echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr><th>Tipo</th><th>Valor</th><th>Válido</th><th>Detalle</th></tr>";
foreach ($pruebas as $p) {
    $result = $server->validarTransaccion($p);
    $detalle = $result['valido'] ? '✅ OK' : '❌ ' . ($result['error'] ?? 'No encontrado');
    if (isset($result['limite_credito'])) {
        $detalle .= ' | Límite: $' . number_format($result['limite_credito'], 0, ',', '.');
    }
    echo "<tr><td>{$p['tipo']}</td><td>" . htmlspecialchars($p['valor']) . "</td><td>" . ($result['valido'] ? '✅' : '❌') . "</td><td>$detalle</td></tr>";
}
echo "</table>";

echo "<h2>📦 Payload de Red (Datos Reales)</h2>";
$payload = new NetworkPayload();
$payload->accion = 'SYNC';
$payload->cuerpoJSON = json_encode($server->obtenerPayload('productos'), JSON_PRETTY_PRINT);
$payload->longitudCuerpo = strlen($payload->cuerpoJSON);

echo "<p><strong>Acción:</strong> {$payload->accion}</p>";
echo "<p><strong>Longitud:</strong> {$payload->longitudCuerpo} bytes</p>";
echo "<p><strong>Delimitador:</strong> <code>" . htmlspecialchars($payload->delimitador) . "</code></p>";
echo "<pre style='background:#222; color:#0f0; padding:10px;'>" . htmlspecialchars($payload->cuerpoJSON) . "</pre>";
