<?php
/**
 * Script de respaldo - Crear usuario admin con contraseña simple
 * Usar solo si actualizar_passwords.php no funciona
 */

require_once __DIR__ . '/config/database.php';

echo "=== Creación de Usuario Administrador de Emergencia ===\n\n";

try {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    // Primero eliminar usuarios existentes
    $query_delete = "DELETE FROM usuarios WHERE email IN ('admin@goapple.com', 'vendedor@goapple.com')";
    $conn->prepare($query_delete)->execute();
    
    // Crear contraseñas con hashes correctos
    $password_admin = 'Admin123';
    $hash_admin = password_hash($password_admin, PASSWORD_DEFAULT);
    
    echo "Creando usuarios...\n";
    echo "Contraseña: $password_admin\n";
    echo "Hash: $hash_admin\n\n";
    
    // Insertar nuevos usuarios
    $query = "INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES 
              (:nombre1, :email1, :password1, :rol1, 'Activo'),
              (:nombre2, :email2, :password2, :rol2, 'Activo')";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':nombre1' => 'Administrador',
        ':email1' => 'admin@goapple.com',
        ':password1' => $hash_admin,
        ':rol1' => 'Administrador',
        ':nombre2' => 'Vendedor Demo',
        ':email2' => 'vendedor@goapple.com',
        ':password2' => $hash_admin,
        ':rol2' => 'Vendedor'
    ]);
    
    echo "✓ Usuarios creados exitosamente\n\n";
    
    // Verificar con password_verify
    echo "Verificando hash...\n";
    if (password_verify($password_admin, $hash_admin)) {
        echo "✓ Hash verificado correctamente\n\n";
    } else {
        echo "✗ Error en verificación del hash\n\n";
    }
    
    echo "=== Credenciales de Acceso ===\n";
    echo "Email: admin@goapple.com\n";
    echo "Contraseña: Admin123\n\n";
    echo "Email: vendedor@goapple.com\n";
    echo "Contraseña: Admin123\n\n";
    
    echo "⚠️ ELIMINA ESTE ARCHIVO después de usarlo\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
