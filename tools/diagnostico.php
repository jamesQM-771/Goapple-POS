<?php
/**
 * Script de Diagnóstico - GOapple
 */

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Diagnóstico - GOapple</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 40px auto; padding: 20px; }
        .ok { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
        .err { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
        h1 { color: #333; }
        h2 { margin-top: 20px; }
        code { background: #f4f4f4; padding: 5px 10px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico del Sistema - GOapple</h1>";

// Test 1: Verificar archivo config.php
echo "<h2>1. Verificando config.php</h2>";
if (file_exists(__DIR__ . '/config/config.php')) {
    echo "<div class='ok'>✓ Archivo config.php existe</div>";
    try {
        require_once __DIR__ . '/config/config.php';
        echo "<div class='ok'>✓ config.php se cargó correctamente</div>";
        echo "<div class='ok'>✓ BASE_URL = " . (defined('BASE_URL') ? BASE_URL : 'NO DEFINIDO') . "</div>";
    } catch (Throwable $e) {
        echo "<div class='err'>✗ Error en config.php: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div class='err'>✗ Archivo config.php NO existe</div>";
}

// Test 2: Verificar session.php
echo "<h2>2. Verificando session.php</h2>";
if (file_exists(__DIR__ . '/config/session.php')) {
    echo "<div class='ok'>✓ Archivo session.php existe</div>";
    try {
        require_once __DIR__ . '/config/session.php';
        echo "<div class='ok'>✓ session.php se cargó correctamente</div>";
    } catch (Throwable $e) {
        echo "<div class='err'>✗ Error en session.php: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div class='err'>✗ Archivo session.php NO existe</div>";
}

// Test 3: Verificar models
echo "<h2>3. Verificando Modelos</h2>";
$modelos = ['Usuario', 'Venta', 'Credito', 'IPhone', 'Cliente'];

foreach ($modelos as $modelo) {
    $archivo = __DIR__ . '/models/' . $modelo . '.php';
    if (file_exists($archivo)) {
        echo "<div class='ok'>✓ $modelo.php existe</div>";
        try {
            require_once $archivo;
            if (class_exists($modelo)) {
                echo "<div class='ok'>✓ Clase $modelo está definida</div>";
            } else {
                echo "<div class='err'>✗ Clase $modelo NO está definida en el archivo</div>";
            }
        } catch (Throwable $e) {
            echo "<div class='err'>✗ Error al cargar $modelo.php: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='err'>✗ $modelo.php NO existe</div>";
    }
}

// Test 4: Verificar vistas
echo "<h2>4. Verificando Vistas</h2>";
$vistas = [
    'layouts/header.php',
    'layouts/footer.php',
];

foreach ($vistas as $vista) {
    $archivo = __DIR__ . '/views/' . $vista;
    if (file_exists($archivo)) {
        echo "<div class='ok'>✓ $vista existe</div>";
    } else {
        echo "<div class='err'>✗ $vista NO existe</div>";
    }
}

// Test 5: Verificar base de datos
echo "<h2>5. Verificando Base de Datos</h2>";
if (file_exists(__DIR__ . '/config/database.php')) {
    echo "<div class='ok'>✓ Archivo database.php existe</div>";
    try {
        require_once __DIR__ . '/config/database.php';
        $database = Database::getInstance();
        $conn = $database->getConnection();
        echo "<div class='ok'>✓ Conexión a base de datos exitosa</div>";
        
        // Verificar tablas
        $tablas = ['usuarios', 'clientes', 'iphones', 'ventas', 'creditos'];
        foreach ($tablas as $tabla) {
            $stmt = $conn->query("SHOW TABLES LIKE '$tabla'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='ok'>✓ Tabla '$tabla' existe</div>";
            } else {
                echo "<div class='err'>✗ Tabla '$tabla' NO existe</div>";
            }
        }
    } catch (Throwable $e) {
        echo "<div class='err'>✗ Error de base de datos: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div class='err'>✗ Archivo database.php NO existe</div>";
}

// Test 6: Resumen
echo "<h2>6. Resumen Final</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>
    <p>Si todos los tests pasaron (✓), entonces el problema está en la sesión o en la lógica del index.php</p>
    <p>Si hay errores (✗), revisa los archivos mencionados.</p>
    <p><strong>Próximo paso:</strong> 
        <a href='login.php'>Ir al login</a> o 
        <a href='index.php'>Intentar index.php nuevamente</a>
    </p>
</div>";

echo "</body></html>";
?>
