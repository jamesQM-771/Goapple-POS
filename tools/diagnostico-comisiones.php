<?php
/**
 * Diagnóstico de Comisiones
 * Verificar si las tablas y datos existen
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/models/Comision.php';

// Si no es admin, no dejar acceso
if (!estaLogueado()) {
    die('Necesitas estar logueado. <a href="views/login.php">Ir al login</a>');
}

if (!esAdmin()) {
    die('Acceso denegado - Solo administradores pueden acceder. <a href="views/dashboard.php">Volver al Dashboard</a>');
}

$db = Database::getInstance();
$conn = $db->getConnection();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Comisiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { padding: 20px; background-color: #f5f5f5; }
        h2 { margin: 30px 0 20px 0; color: #333; }
        h3 { margin: 20px 0 15px 0; color: #555; }
        table { font-size: 14px; }
        pre { background-color: #f9f9f9; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="bi bi-clipboard-check"></i> Diagnóstico de Comisiones</h2>

    <?php
    // 1. Verificar tabla comision_config
    echo "<h3>1. Tabla comision_config</h3>";
try {
    $stmt = $conn->prepare("SHOW TABLES LIKE 'comision_config'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> ✓ Tabla existe</div>";
            
            // Ver estructura
            $stmt = $conn->prepare("DESCRIBE comision_config");
            $stmt->execute();
            echo "<table class='table table-sm table-bordered'>";
            echo "<thead class='table-light'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr></thead>";
            echo "<tbody>";
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-danger'><i class='bi bi-exclamation-circle'></i> ✗ Tabla NO existe - Necesita ejecutar database.sql</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle'></i> Error: " . htmlspecialchars($e->getMessage()) . "</div>";
try {
    $usuarioModel = new Usuario();
    $vendedores = $usuarioModel->obtenerTodos(['rol' => 'vendedor']);
    
    if (!empty($vendedores)) {
        echo "<p style='color: green;'>✓ Se encontraron " . count($vendedores) . " vendedores</p>";
        echo "<ul>";
        foreach ($vendedores as $v) {
            echo "<li>" . htmlspecialchars($v['nombre']) . " (ID: " . $v['id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ No hay vendedores creados</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// 3. Verificar configuraciones de comisión
echo "<h3>3. Configuraciones de Comisión</h3>";
try {
    $comisionModel = new Comision();
    $stmt = $conn->prepare("SELECT * FROM comision_config");
    $stmt->execute();
    $configs = $stmt->fetchAll();
    
    if (!empty($configs)) {
        echo "<p style='color: green;'>✓ Se encontraron " . count($configs) . " configuraciones</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Vendedor</th><th>Porcentaje</th><th>Meta</th><th>Bono</th><th>Descuento</th><th>Retención</th></tr>";
        foreach ($configs as $cfg) {
            echo "<tr>";
            echo "<td>" . $cfg['id'] . "</td>";
            echo "<td>" . $cfg['vendedor_id'] . "</td>";
            echo "<td>" . $cfg['porcentaje'] . "%</td>";
            echo "<td>$" . $cfg['meta_mensual'] . "</td>";
            echo "<td>$" . $cfg['bono_meta'] . "</td>";
            echo "<td>$" . $cfg['descuento_fijo'] . "</td>";
            echo "<td>" . $cfg['retencion_pct'] . "%</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ No hay configuraciones de comisión creadas aún</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// 4. Verificar método obtenerConfig
echo "<h3>4. Prueba método obtenerConfig()</h3>";
try {
    $comisionModel = new Comision();
    if (!empty($vendedores) && !empty($vendedores[0])) {
        $id = $vendedores[0]['id'];
        $cfg = $comisionModel->obtenerConfig($id);
        echo "<p style='color: green;'>✓ Método funciona</p>";
        echo "<pre>" . print_r($cfg, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// 5. Verificar tabla comision_pagos
echo "<h3>5. Tabla comision_pagos</h3>";
try {
    $stmt = $conn->prepare("SHOW TABLES LIKE 'comision_pagos'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Tabla existe</p>";
    } else {
        echo "<p style='color: red;'>✗ Tabla NO existe</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Navegación:</strong></p>";
echo "<ul>";
echo "<li><a href='views/comisiones/config.php' class='btn btn-primary'>Ir a Configuración de Comisiones</a></li>";
echo "<li><a href='views/comisiones/reportes.php' class='btn btn-info'>Ir a Reportes de Comisiones</a></li>";
echo "<li><a href='views/dashboard.php' class='btn btn-secondary'>Volver al Dashboard</a></li>";
echo "</ul>";
?>
</div>
</body>
</html>

