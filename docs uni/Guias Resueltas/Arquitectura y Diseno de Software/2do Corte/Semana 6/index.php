<?php
header("Content-Type: text/html; charset=UTF-8");

echo "<h1>📱 Caso Testigo: Inserción de Producto en GoApple POS</h1>";
echo "<p>La entidad <code>Producto</code> se registra en la tabla <code>iphones</code> de la base de datos real mediante el <code>ProductoDAO</code>.</p>";

require_once 'app/Core/Producto.php';
require_once 'app/Data/ProductoDAO.php';

$accion = $_GET['accion'] ?? '';

if ($accion === 'insertar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $nuevoProducto = new Core\Producto(
        $_GET['modelo'] ?? 'iPhone 16 Pro Max',
        $_GET['capacidad'] ?? '512GB',
        $_GET['color'] ?? 'Titanio Negro',
        $_GET['condicion'] ?? 'nuevo',
        $_GET['imei'] ?? 'IMEI-' . uniqid(),
        (float)($_GET['precio_compra'] ?? 5000000),
        (float)($_GET['precio_venta'] ?? 7200000)
    );

    $gestorIntermedio = new Data\ProductoDAO();
    $resultadoTransaccion = $gestorIntermedio->registrar($nuevoProducto);

    if ($resultadoTransaccion) {
        echo "<p style='color:green;font-size:1.2em;'>✅ [Motor Storage Ok] Producto insertado exitosamente en la tabla <code>iphones</code>.</p>";
    } else {
        echo "<p style='color:red;font-size:1.2em;'>❌ Error al insertar el producto. Verifica que el IMEI no esté duplicado.</p>";
    }

    echo "<hr><p><a href='?'>Volver al listado</a></p>";
}

echo "<h2>📋 Inventario Actual (GoApple POS)</h2>";

try {
    $dao = new Data\ProductoDAO();
    $productos = $dao->listar();

    if ($productos) {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
        echo "<tr style='background:#333;color:white;'>
                <th>ID</th><th>Modelo</th><th>Capacidad</th><th>Color</th><th>IMEI</th><th>Precio Venta</th><th>Estado</th>
              </tr>";
        foreach ($productos as $p) {
            echo "<tr>";
            echo "<td>" . $p['id'] . "</td>";
            echo "<td>" . htmlspecialchars($p['modelo']) . "</td>";
            echo "<td>" . htmlspecialchars($p['capacidad']) . "</td>";
            echo "<td>" . htmlspecialchars($p['color']) . "</td>";
            echo "<td>" . htmlspecialchars($p['imei']) . "</td>";
            echo "<td>$" . number_format($p['precio_venta'], 0, ',', '.') . "</td>";
            echo "<td>" . $p['estado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay productos en el inventario.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr><h3>➕ Insertar Nuevo Producto</h3>";
echo "<form method='get' style='border:1px solid #ccc;padding:15px;border-radius:5px;'>";
echo "<input type='hidden' name='accion' value='insertar'>";
echo "<table>";
echo "<tr><td>Modelo:</td><td><input type='text' name='modelo' value='iPhone 16 Pro Max' required></td></tr>";
echo "<tr><td>Capacidad:</td><td><input type='text' name='capacidad' value='512GB' required></td></tr>";
echo "<tr><td>Color:</td><td><input type='text' name='color' value='Titanio Negro' required></td></tr>";
echo "<tr><td>Condición:</td><td>
        <select name='condicion'>
            <option value='nuevo'>Nuevo</option>
            <option value='usado'>Usado</option>
        </select>
      </td></tr>";
echo "<tr><td>IMEI:</td><td><input type='text' name='imei' value='IMEI-" . uniqid() . "' required></td></tr>";
echo "<tr><td>Precio Compra:</td><td><input type='number' name='precio_compra' value='5000000' required></td></tr>";
echo "<tr><td>Precio Venta:</td><td><input type='number' name='precio_venta' value='7200000' required></td></tr>";
echo "<tr><td colspan='2'><button type='submit'>Insertar Producto</button></td></tr>";
echo "</table>";
echo "</form>";
