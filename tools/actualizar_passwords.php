<?php
/**
 * Script para actualizar contraseñas de usuarios
 * Ejecutar una sola vez para corregir los hashes
 */

require_once __DIR__ . '/config/database.php';

echo "=== Actualización de Contraseñas ===\n\n";

try {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    // Generar hash correcto para Admin123
    $password = 'Admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "Nueva contraseña: $password\n";
    echo "Nuevo hash: $hash\n\n";
    
    // Actualizar usuarios
    $query = "UPDATE usuarios SET password = :password WHERE email IN ('admin@goapple.com', 'vendedor@goapple.com')";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $hash);
    
    if ($stmt->execute()) {
        $count = $stmt->rowCount();
        echo "✓ Contraseñas actualizadas correctamente ($count usuarios)\n\n";
        
        // Verificar
        $query_check = "SELECT nombre, email FROM usuarios WHERE email IN ('admin@goapple.com', 'vendedor@goapple.com')";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->execute();
        $usuarios = $stmt_check->fetchAll();
        
        echo "Usuarios actualizados:\n";
        foreach ($usuarios as $usuario) {
            echo "  - {$usuario['nombre']} ({$usuario['email']})\n";
        }
        
        echo "\n✓ Proceso completado exitosamente\n";
        echo "\nPuedes iniciar sesión con:\n";
        echo "  Email: admin@goapple.com\n";
        echo "  Contraseña: Admin123\n\n";
        
        echo "⚠️ IMPORTANTE: Elimina este archivo después de ejecutarlo por seguridad\n";
        
    } else {
        echo "✗ Error al actualizar contraseñas\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
