<?php
/**
 * Script para agregar tablas faltantes
 * Ejecutar UNA SOLA VEZ desde el navegador
 */

// Permitir errores para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Actualizar Base de Datos - GOapple</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0; }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 20px; }
        h3 { color: #666; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        ul { padding-left: 20px; }
        li { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>🔧 Actualización de Base de Datos - GOapple POS</h1>";

try {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    echo "<div class='info'>✓ Conexión a base de datos establecida</div>";
    
    // PASO 1: Verificar que las tablas principales existen
    echo "<h2>1. Verificando tablas principales...</h2>";
    
    $tablas_requeridas = ['usuarios', 'clientes', 'iphones', 'ventas', 'creditos'];
    $todas_existen = true;
    
    foreach ($tablas_requeridas as $tabla) {
        $sql = "SHOW TABLES LIKE '$tabla'";
        $stmt = $conn->query($sql);
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>✓ Tabla <strong>$tabla</strong> existe</div>";
        } else {
            echo "<div class='error'>✗ Tabla <strong>$tabla</strong> NO existe</div>";
            $todas_existen = false;
        }
    }
    
    if (!$todas_existen) {
        throw new Exception("Faltan tablas principales. Ejecuta database.sql primero.");
    }
    
    // PASO 2: Deshabilitar restricciones de claves foráneas temporalmente
    echo "<h2>2. Preparando base de datos...</h2>";
    $conn->exec("SET FOREIGN_KEY_CHECKS=0");
    echo "<div class='warning'>⚠ Restricciones de claves foráneas deshabilitadas temporalmente</div>";
    
    // PASO 3: Crear tabla credito_pagos
    echo "<h2>3. Creando tabla credito_pagos...</h2>";
    
    $sql = "DROP TABLE IF EXISTS credito_pagos";
    $conn->exec($sql);
    
    $sql = "CREATE TABLE credito_pagos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        credito_id INT NOT NULL,
        monto_pago DECIMAL(12,2) NOT NULL,
        metodo_pago ENUM('efectivo', 'transferencia', 'tarjeta') DEFAULT 'efectivo',
        observaciones TEXT,
        fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_credito (credito_id),
        INDEX idx_fecha (fecha_pago)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql);
    echo "<div class='success'>✓ Tabla credito_pagos creada exitosamente</div>";
    
    // PASO 4: Crear tabla credito_cuotas
    echo "<h2>4. Creando tabla credito_cuotas...</h2>";
    
    $sql = "DROP TABLE IF EXISTS credito_cuotas";
    $conn->exec($sql);
    
    $sql = "CREATE TABLE credito_cuotas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        credito_id INT NOT NULL,
        numero_cuota INT NOT NULL,
        monto_cuota DECIMAL(12,2) NOT NULL,
        fecha_vencimiento DATE NOT NULL,
        fecha_pago DATE,
        estado ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente',
        INDEX idx_credito (credito_id),
        INDEX idx_cuota (numero_cuota),
        INDEX idx_vencimiento (fecha_vencimiento)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->exec($sql);
    echo "<div class='success'>✓ Tabla credito_cuotas creada exitosamente</div>";
    
    // PASO 5: Verificar y agregar campo documento a clientes
    echo "<h2>5. Verificando tabla clientes...</h2>";
    
    $sql = "SHOW COLUMNS FROM clientes LIKE 'documento'";
    $stmt = $conn->query($sql);
    
    if ($stmt->rowCount() == 0) {
        echo "<p>Campo 'documento' no existe, agregando...</p>";
        $sql = "ALTER TABLE clientes ADD COLUMN documento VARCHAR(50) UNIQUE";
        $conn->exec($sql);
        
        // Copiar valores de cedula si existen
        $sql = "UPDATE clientes SET documento = cedula WHERE documento IS NULL OR documento = ''";
        $conn->exec($sql);
        
        echo "<div class='success'>✓ Campo 'documento' agregado y valores copiados desde 'cedula'</div>";
    } else {
        echo "<div class='info'>✓ Campo 'documento' ya existe</div>";
    }
    
    // PASO 6: Verificar campos necesarios en ventas
    echo "<h2>6. Verificando tabla ventas...</h2>";
    
    $campos_ventas = ['cliente_id', 'vendedor_id', 'tipo_venta', 'subtotal', 'total', 'forma_pago'];
    foreach ($campos_ventas as $campo) {
        $sql = "SHOW COLUMNS FROM ventas LIKE '$campo'";
        $stmt = $conn->query($sql);
        if ($stmt->rowCount() > 0) {
            echo "<div class='info'>✓ Campo <strong>$campo</strong> existe</div>";
        } else {
            echo "<div class='warning'>⚠ Campo <strong>$campo</strong> no encontrado</div>";
        }
    }
    
    // PASO 7: Habilitar restricciones de claves foráneas nuevamente
    echo "<h2>7. Finalizando...</h2>";
    $conn->exec("SET FOREIGN_KEY_CHECKS=1");
    echo "<div class='success'>✓ Restricciones de claves foráneas restauradas</div>";
    
    // PASO 8: Resumen final
    echo "<h2>Resumen de tablas en la base de datos:</h2>";
    
    $sql = "SHOW TABLES";
    $stmt = $conn->query($sql);
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tablas as $tabla) {
        echo "<li style='color: green;'>✓ <strong>$tabla</strong></li>";
    }
    echo "</ul>";
    
    echo "<div class='success' style='margin-top: 30px; padding: 20px;'>
        <h2>✅ ¡Actualización completada con éxito!</h2>
        <p>La base de datos ha sido actualizada correctamente.</p>
        <hr>
        <h3>Próximos pasos:</h3>
        <ol>
            <li>Sube todos los archivos modificados al servidor vía FTP</li>
            <li>Accede al sistema: <a href='login.php' style='color: #007bff; font-weight: bold;'>login.php</a></li>
            <li>Credenciales: <strong>admin</strong> / <strong>Admin123</strong></li>
            <li>Verifica que aparezcan los módulos: Ventas, Créditos, Reportes</li>
        </ol>
        <hr>
        <p style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>
            <strong>💡 Nota:</strong> Este script solo necesita ejecutarse una vez. 
            Si todo está correcto, puedes eliminar <code>actualizar_db.php</code> después.
        </p>
    </div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>
        <h3>❌ Error de Base de Datos:</h3>
        <pre>" . htmlspecialchars($e->getMessage()) . "</pre>
        <h4>Posibles soluciones:</h4>
        <ul>
            <li>Verifica que la base de datos <strong>giorgiju_goapple_pos</strong> existe</li>
            <li>Verifica las credenciales en <strong>config/database.php</strong></li>
            <li>Asegúrate de que el usuario tiene permisos para crear/modificar tablas</li>
            <li>Si la base de datos está vacía, corre primero <strong>database.sql</strong> completo</li>
        </ul>
        <p><strong>Código de error:</strong> " . htmlspecialchars($e->getCode()) . "</p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
        <h3>❌ Error General:</h3>
        <pre>" . htmlspecialchars($e->getMessage()) . "</pre>
        <p><strong>Solución:</strong> Asegúrate de ejecutar database.sql primero para crear todas las tablas.</p>
    </div>";
}

echo "</body></html>";
?>
