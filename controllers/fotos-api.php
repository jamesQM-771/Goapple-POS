<?php
/**
 * API para manejo de fotos
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Foto.php';

// Log de request
error_log("API Fotos - POST: " . print_r($_POST, true));
error_log("API Fotos - FILES: " . print_r($_FILES, true));

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    try {
        if ($accion === 'upload_compra') {
            // Upload de fotos de compra
            if (!isset($_FILES['archivo'])) {
                error_log("Error: No se encontró archivo en upload_compra");
                echo json_encode(['success' => false, 'message' => 'No se encontró archivo']);
                exit;
            }
            
            $iphone_id = intval($_POST['iphone_id'] ?? 0);
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            
            if ($iphone_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'iPhone inválido']);
                exit;
            }
            
            // Procesar archivo
            $resultado = Foto::procesarFoto($_FILES['archivo'], 'compras');
            
            if ($resultado['success']) {
                $fotoModel = new Foto();
                $upload = $fotoModel->cargarFotoCompra(
                    $iphone_id,
                    $resultado['archivo'],
                    $descripcion,
                    usuarioActual()['id'] ?? null
                );
                
                echo json_encode($upload);
            } else {
                echo json_encode($resultado);
            }
            
        } elseif ($accion === 'upload_venta') {
            // Upload de fotos de venta
            if (!isset($_FILES['archivo'])) {
                error_log("Error: No se encontró archivo en upload_venta");
                echo json_encode(['success' => false, 'message' => 'No se encontró archivo']);
                exit;
            }
            
            $venta_id = intval($_POST['venta_id'] ?? 0);
            $iphone_id = intval($_POST['iphone_id'] ?? 0);
            $descripcion = sanitizar($_POST['descripcion'] ?? '');
            
            if ($venta_id <= 0) {
                error_log("Error: Venta ID inválida: $venta_id");
                echo json_encode(['success' => false, 'message' => 'Venta inválida']);
                exit;
            }
            
            error_log("Procesando foto para venta_id: $venta_id");
            
            // Procesar archivo
            $resultado = Foto::procesarFoto($_FILES['archivo'], 'ventas');
            
            error_log("Resultado procesarFoto: " . json_encode($resultado));
            
            if ($resultado['success']) {
                $fotoModel = new Foto();
                $upload = $fotoModel->cargarFotoVenta(
                    $venta_id,
                    $iphone_id > 0 ? $iphone_id : null,
                    $resultado['archivo'],
                    $descripcion,
                    usuarioActual()['id'] ?? null
                );
                
                error_log("Resultado cargarFotoVenta: " . json_encode($upload));
                echo json_encode($upload);
            } else {
                echo json_encode($resultado);
            }
            
        } elseif ($accion === 'eliminar') {
            $foto_id = intval($_POST['id'] ?? $_POST['foto_id'] ?? 0);
            $tabla = $_POST['tabla'] ?? 'fotos_compra';
            
            if ($foto_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de foto inválido']);
                exit;
            }
            
            if (!in_array($tabla, ['fotos_compra', 'fotos_venta'])) {
                echo json_encode(['success' => false, 'message' => 'Tabla inválida']);
                exit;
            }
            
            $fotoModel = new Foto();
            $resultado = $fotoModel->eliminarFoto($foto_id, $tabla);
            echo json_encode($resultado);
        } else {
            echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
        }
    } catch (Exception $e) {
        error_log("Error en API de fotos: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
