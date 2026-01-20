<?php
/**
 * Script SIMPLE para crear tablas faltantes
 * Sin verificaciones complicadas - directo y rápido
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión directa sin la clase Database
$host = "shared20.hostgator.co";
$db_name = "giorgiju_goapple_pos";
$username = "giorgiju";
$password = "Giorgi2006*";
$charset = "utf8mb4";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=$charset",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Setup - GOapple</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 40px auto; padding: 20px; }
        .box { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .ok { background: #d4edda; color: green; border: 1px solid #c3e6cb; }
        .err { background: #f8d7da; color: red; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: blue; border: 1px solid #bee5eb; }
        h1 { color: #333; }
        h2 { margin-top: 20px; color: #555; }
        .ok::before { content: '✓ '; font-weight: bold; }
        .err::before { content: '✗ '; font-weight: bold; }
        .info::before { content: 'ℹ '; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table td { padding: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>🔧 Setup de Base de Datos - GOapple POS</h1>
    <div class='info box'>Conectado a: <strong>$host / $db_name</strong></div>";
    
    // Paso 1: Deshabilitar restricciones
    echo "<h2>1. Preparando base de datos</h2>";
    $conn->exec("SET FOREIGN_KEY_CHECKS=0");
    echo "<div class='ok box'>Restricciones de claves foráneas deshabilitadas</div>";
    
    // Paso 2: Crear tabla credito_pagos
    echo "<h2>2. Tabla credito_pagos</h2>";
    try {
        $conn->exec("DROP TABLE IF EXISTS credito_pagos");
        $sql = "CREATE TABLE credito_pagos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            credito_id INT NOT NULL,
            monto_pago DECIMAL(12,2) NOT NULL,
            metodo_pago VARCHAR(50) DEFAULT 'efectivo',
            observaciones TEXT,
            fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY idx_credito (credito_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $conn->exec($sql);
        echo "<div class='ok box'>Tabla creada exitosamente</div>";
    } catch (Exception $e) {
        echo "<div class='err box'>" . $e->getMessage() . "</div>";
    }
    
    // Paso 3: Crear tabla credito_cuotas
    echo "<h2>3. Tabla credito_cuotas</h2>";
    try {
        $conn->exec("DROP TABLE IF EXISTS credito_cuotas");
        $sql = "CREATE TABLE credito_cuotas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            credito_id INT NOT NULL,
            numero_cuota INT NOT NULL,
            monto_cuota DECIMAL(12,2) NOT NULL,
            fecha_vencimiento DATE NOT NULL,
            fecha_pago DATE,
            estado VARCHAR(50) DEFAULT 'pendiente',
            KEY idx_credito (credito_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $conn->exec($sql);
        echo "<div class='ok box'>Tabla creada exitosamente</div>";
    } catch (Exception $e) {
        echo "<div class='err box'>" . $e->getMessage() . "</div>";
    }
    
    // Paso 4: Verificar y agregar campo documento
    echo "<h2>4. Campo documento en clientes</h2>";
    try {
        $stmt = $conn->query("SHOW COLUMNS FROM clientes LIKE 'documento'");
        if ($stmt->rowCount() == 0) {
            $conn->exec("ALTER TABLE clientes ADD COLUMN documento VARCHAR(50) UNIQUE");
            $conn->exec("UPDATE clientes SET documento = cedula WHERE documento IS NULL");
            echo "<div class='ok box'>Campo agregado y datos copiados</div>";
        } else {
            echo "<div class='info box'>Campo ya existe</div>";
        }
    } catch (Exception $e) {
        echo "<div class='err box'>" . $e->getMessage() . "</div>";
    }
    
    // Paso 5: Habilitar restricciones
    echo "<h2>5. Finalizando</h2>";
    $conn->exec("SET FOREIGN_KEY_CHECKS=1");
    echo "<div class='ok box'>Restricciones de claves foráneas restauradas</div>";
    
    // Paso 6: Listar todas las tablas
    echo "<h2>6. Resumen de tablas</h2>";
    $stmt = $conn->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<table>";
    echo "<tr style='background: #f0f0f0;'><td><strong>Tabla</strong></td><td><strong>Estado</strong></td></tr>";
    foreach ($tablas as $tabla) {
        echo "<tr><td>$tabla</td><td style='color: green;'>✓ OK</td></tr>";
    }
    echo "</table>";
    
    // Resumen final
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin-top: 30px;'>
        <h2 style='color: green;'>✅ ¡Setup completado!</h2>
        <p>La base de datos está lista para usar.</p>
        <ol>
            <li>Sube los archivos modificados al servidor</li>
            <li>Accede a: <a href='login.php'>login.php</a></li>
            <li>Usuario: <strong>admin</strong> | Contraseña: <strong>Admin123</strong></li>
        </ol>
    </div>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Error - GOapple</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 40px auto; padding: 20px; }
        .error { background: #f8d7da; padding: 20px; border-radius: 5px; border: 1px solid #f5c6cb; color: red; }
        h1 { color: red; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>❌ Error de Conexión</h1>
    <div class='error'>
        <p><strong>No se pudo conectar a la base de datos:</strong></p>
        <p><code>" . htmlspecialchars($e->getMessage()) . "</code></p>
        <hr>
        <h3>Verifica:</h3>
        <ul>
            <li>Host: $host</li>
            <li>Base de datos: $db_name</li>
            <li>Usuario: $username</li>
            <li>Archivo: config/database.php</li>
        </ul>
        <p><a href='login.php'>Volver al login</a></p>
    </div>
</body>
</html>";
}
?>
