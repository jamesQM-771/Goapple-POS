<?php
require_once __DIR__ . '/../config/database.php';

$email = 'admin@goapple.com';
$nueva_pass = 'admin123';

try {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    $hash = password_hash($nueva_pass, PASSWORD_DEFAULT);
    
    $query = "UPDATE usuarios SET password = :pass, estado = 'activo' WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':pass', $hash);
    $stmt->bindParam(':email', $email);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo "✓ Contraseña de $email restablecida a: $nueva_pass\n";
        } else {
            echo "✗ No se encontró el usuario $email o la contraseña ya era esa.\n";
        }
    } else {
        echo "✗ Error al ejecutar la actualización.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
