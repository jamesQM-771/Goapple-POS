<?php
// Script para generar hashes de contraseñas
// Ejecutar: php generar_hash.php

$password = 'Admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña: $password\n";
echo "Hash: $hash\n\n";

// Verificar que funciona
if (password_verify($password, $hash)) {
    echo "✓ Hash verificado correctamente\n";
} else {
    echo "✗ Error en verificación\n";
}
?>
