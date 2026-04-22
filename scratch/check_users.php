<?php
require_once __DIR__ . '/../config/database.php';

try {
    $database = Database::getInstance();
    $conn = $database->getConnection();
    
    $query = "SELECT id, email, nombre, rol, estado FROM usuarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== Usuarios en la Base de Datos ===\n";
    foreach ($usuarios as $u) {
        echo "ID: {$u['id']} | Email: {$u['email']} | Nombre: {$u['nombre']} | Rol: {$u['rol']} | Estado: {$u['estado']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
