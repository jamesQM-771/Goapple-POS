<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: james | Guía: 4 */
require_once __DIR__ . '/../../../../../config/database.php';
require_once __DIR__ . '/app/Core/Validacion.php';

use Core\Validacion;

echo "<h1>🔍 Capa de Validación — GoApple POS</h1>";
echo "<p>Demostración de la clase <code>Validacion</code> sanitizando y validando datos reales de la base de datos.</p>";

try {
    $db = Database::getInstance()->getConnection();

    // 1. Validar datos de clientes reales
    echo "<h2>📋 Validación de Clientes</h2>";
    $stmt = $db->query("SELECT id, nombre, cedula, telefono, email FROM clientes LIMIT 5");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($clientes) {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Cédula</th><th>Teléfono</th><th>Email</th></tr>";
        foreach ($clientes as $c) {
            $cedulaValida = Validacion::validarCedula($c['cedula']) ? '✅' : '❌';
            $telValido = Validacion::validarTelefono($c['telefono']) ? '✅' : '❌';
            $emailValido = Validacion::validarEmail($c['email'] ?? '') ? '✅' : '❌';
            echo "<tr>";
            echo "<td>" . $c['id'] . "</td>";
            echo "<td>" . Validacion::sanitizarEntrada($c['nombre']) . "</td>";
            echo "<td>" . Validacion::sanitizarEntrada($c['cedula']) . " $cedulaValida</td>";
            echo "<td>" . Validacion::sanitizarEntrada($c['telefono']) . " $telValido</td>";
            echo "<td>" . Validacion::sanitizarEntrada($c['email'] ?? '') . " $emailValido</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // 2. Validar precios de iPhones
    echo "<h2>💲 Validación de Precios (iPhones)</h2>";
    $stmt = $db->query("SELECT id, modelo, precio_venta FROM iphones LIMIT 5");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Modelo</th><th>Precio</th><th>Válido</th></tr>";
        foreach ($productos as $p) {
            $precioValido = Validacion::validarPrecio($p['precio_venta']) ? '✅ Precio válido' : '❌ Precio inválido';
            echo "<tr>";
            echo "<td>" . $p['id'] . "</td>";
            echo "<td>" . Validacion::sanitizarEntrada($p['modelo']) . "</td>";
            echo "<td>$" . number_format($p['precio_venta'], 0, ',', '.') . "</td>";
            echo "<td>$precioValido</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // 3. Demostración de sanitización
    echo "<h2>🧹 Demostración de Sanitización</h2>";
    $entradasPeligrosas = [
        "  texto con espacios  ",
        "<script>alert('xss')</script>",
        "cadena con 'comillas' y \"dobles\"",
        "  otro texto con espacios  ",
    ];

    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>Entrada Original</th><th>Sanitizada</th></tr>";
    foreach ($entradasPeligrosas as $entrada) {
        echo "<tr>";
        echo "<td><code>" . htmlspecialchars($entrada) . "</code></td>";
        echo "<td><code>" . Validacion::sanitizarEntrada($entrada) . "</code></td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
