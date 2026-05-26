<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>📦 Serialización de Objetos — GoApple POS (Stub & Marshal)</h1>";
echo "<p>Demostración de serialización/deserialización de objetos con datos reales de la BD.</p>";

require_once __DIR__ . '/../../../../../config/database.php';

class Producto {
    public $id;
    public $modelo;
    public $capacidad;
    public $color;
    public $imei;
    public $precioVenta;
    public $estado;

    public function __construct($id, $modelo, $capacidad, $color, $imei, $precioVenta, $estado) {
        $this->id = $id;
        $this->modelo = $modelo;
        $this->capacidad = $capacidad;
        $this->color = $color;
        $this->imei = $imei;
        $this->precioVenta = $precioVenta;
        $this->estado = $estado;
    }
}

class ClientStub {
    public function enviarObjeto(Producto $producto) {
        $payloadMarshaled = serialize($producto);
        echo "<p><strong>Objeto Serializado (Stub):</strong></p>";
        echo "<pre style='background:#222; color:#0f0; padding:10px;'>" . htmlspecialchars($payloadMarshaled) . "</pre>";
        echo "<p>🔹 <strong>Formato:</strong> String serializado de PHP (O:8:\"Producto\":7:{...})</p>";
        echo "<p>🔹 <strong>Tamaño:</strong> " . strlen($payloadMarshaled) . " bytes</p>";
        return $payloadMarshaled;
    }
}

class ServerUnmarshaling {
    public function recibirObjeto($payloadBytes) {
        echo "<p><strong>Objeto Reconstruido en Servidor (Unmarshal):</strong></p>";
        $producto = unserialize($payloadBytes);
        if ($producto instanceof Producto) {
            echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
            echo "<tr><th>Campo</th><th>Valor</th></tr>";
            echo "<tr><td>ID</td><td>{$producto->id}</td></tr>";
            echo "<tr><td>Modelo</td><td>" . htmlspecialchars($producto->modelo) . "</td></tr>";
            echo "<tr><td>Capacidad</td><td>" . htmlspecialchars($producto->capacidad) . "</td></tr>";
            echo "<tr><td>Color</td><td>" . htmlspecialchars($producto->color) . "</td></tr>";
            echo "<tr><td>IMEI</td><td>" . htmlspecialchars($producto->imei) . "</td></tr>";
            echo "<tr><td>Precio</td><td>$" . number_format($producto->precioVenta, 0, ',', '.') . "</td></tr>";
            echo "<tr><td>Estado</td><td>{$producto->estado}</td></tr>";
            echo "</table>";
        }
    }
}

echo "<h2>📋 Datos desde la BD Real</h2>";

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, modelo, capacidad, color, imei, precio_venta, estado FROM iphones ORDER BY id DESC LIMIT 5");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        echo "<p>Selecciona un producto para serializar:</p>";
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Modelo</th><th>Serializar</th></tr>";
        foreach ($productos as $p) {
            echo "<tr>";
            echo "<td>{$p['id']}</td>";
            echo "<td>" . htmlspecialchars($p['modelo']) . "</td>";
            echo "<td><a href='?id={$p['id']}'>🔗 Serializar</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $db->prepare("SELECT * FROM iphones WHERE id = ?");
        $stmt->execute([$id]);
        $prodData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prodData) {
            $prod = new Producto(
                $prodData['id'],
                $prodData['modelo'],
                $prodData['capacidad'],
                $prodData['color'],
                $prodData['imei'],
                (float)$prodData['precio_venta'],
                $prodData['estado']
            );

            echo "<hr><h2>🔄 Flujo Completo de Serialización</h2>";

            $stub = new ClientStub();
            $bytes = $stub->enviarObjeto($prod);

            $server = new ServerUnmarshaling();
            $server->recibirObjeto($bytes);

            echo "<p style='color:green;margin-top:15px;'>✅ Marshaling/Unmarshaling completado con datos reales.</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
