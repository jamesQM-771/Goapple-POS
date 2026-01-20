<?php
/**
 * Script de diagnóstico para problemas de fotos
 */

require_once __DIR__ . '/config/config.php';

echo "<h2>Diagnóstico del Sistema de Fotos</h2>";
echo "<hr>";

// 1. Verificar directorio /uploads
$uploadsDir = __DIR__ . '/uploads';
echo "<h3>1. Directorio /uploads</h3>";
if (is_dir($uploadsDir)) {
    echo "✅ Directorio existe: <code>$uploadsDir</code><br>";
    $permisos = substr(sprintf('%o', fileperms($uploadsDir)), -4);
    echo "Permisos: <code>$permisos</code><br>";
    
    if (is_writable($uploadsDir)) {
        echo "✅ Es escribible<br>";
    } else {
        echo "❌ NO es escribible<br>";
    }
} else {
    echo "❌ Directorio NO existe<br>";
    echo "Intentando crear...<br>";
    if (mkdir($uploadsDir, 0755, true)) {
        echo "✅ Directorio creado<br>";
    } else {
        echo "❌ No se pudo crear<br>";
    }
}

// 2. Verificar subdirectorios
echo "<h3>2. Subdirectorios</h3>";
$subdirs = ['fotos', 'compras', 'ventas'];
foreach ($subdirs as $subdir) {
    $path = $uploadsDir . '/' . $subdir;
    if (is_dir($path)) {
        echo "✅ <code>$subdir</code> existe<br>";
    } else {
        echo "⚠️ <code>$subdir</code> NO existe<br>";
    }
}

// 3. Verificar fotos en la BD
echo "<h3>3. Fotos en Base de Datos</h3>";
require_once __DIR__ . '/models/Foto.php';

$fotoModel = new Foto();

// Fotos de venta
$ventaFotos = $fotoModel->obtenerFotosVenta(18); // De la venta de la captura
echo "Fotos de venta ID 18:<br>";
if (!empty($ventaFotos)) {
    foreach ($ventaFotos as $foto) {
        echo "- ID: {$foto['id']}, Archivo: <code>{$foto['archivo']}</code><br>";
        $archivoCompleto = $uploadsDir . '/' . $foto['archivo'];
        if (file_exists($archivoCompleto)) {
            echo "  ✅ Archivo existe<br>";
        } else {
            echo "  ❌ Archivo NO existe: <code>$archivoCompleto</code><br>";
        }
    }
} else {
    echo "❌ Sin fotos en la BD<br>";
}

// 4. Verificar permisos de escritura para PHP
echo "<h3>4. Usuario PHP y Permisos</h3>";
echo "Usuario PHP: " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'No disponible') . "<br>";
echo "Grupo PHP: " . (function_exists('posix_getgrgid') ? posix_getgrgid(posix_getegid())['name'] : 'No disponible') . "<br>";

// 5. Test de subida
echo "<h3>5. Test de Subida</h3>";
$testDir = $uploadsDir . '/test';
if (!is_dir($testDir)) {
    if (mkdir($testDir, 0755, true)) {
        echo "✅ Se creó directorio de test<br>";
    } else {
        echo "❌ No se pudo crear directorio de test<br>";
    }
}

$testFile = $testDir . '/test_' . uniqid() . '.txt';
if (file_put_contents($testFile, 'test')) {
    echo "✅ Se puede escribir archivos<br>";
    unlink($testFile);
} else {
    echo "❌ NO se puede escribir archivos<br>";
}

// 6. Verificar PHP ini settings
echo "<h3>6. PHP Settings</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'default') . "<br>";

echo "<hr>";
echo "<a href='javascript:history.back()'>← Volver</a>";
?>
