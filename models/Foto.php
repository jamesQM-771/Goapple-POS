<?php

require_once __DIR__ . '/../config/database.php';

class Foto
{
    private $conn;

    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Cargar foto de compra
     */
    public function cargarFotoCompra($iphone_id, $archivo, $descripcion = '', $usuario_id = null)
    {
        try {
            $query = "INSERT INTO fotos_compra (iphone_id, archivo, descripcion, usuario_id) 
                      VALUES (:iphone_id, :archivo, :descripcion, :usuario_id)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':iphone_id', $iphone_id);
            $stmt->bindParam(':archivo', $archivo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':usuario_id', $usuario_id);
            
            $stmt->execute();
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            error_log("Error al cargar foto de compra: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cargar foto'];
        }
    }

    /**
     * Cargar foto de venta
     */
    public function cargarFotoVenta($venta_id, $iphone_id, $archivo, $descripcion = '', $usuario_id = null)
    {
        try {
            $query = "INSERT INTO fotos_venta (venta_id, iphone_id, archivo, descripcion, usuario_id) 
                      VALUES (:venta_id, :iphone_id, :archivo, :descripcion, :usuario_id)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':venta_id', $venta_id);
            $stmt->bindParam(':iphone_id', $iphone_id);
            $stmt->bindParam(':archivo', $archivo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':usuario_id', $usuario_id);
            
            $stmt->execute();
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            error_log("Error al cargar foto de venta: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cargar foto'];
        }
    }

    /**
     * Obtener fotos de compra de un iPhone
     */
    public function obtenerFotosCompra($iphone_id)
    {
        try {
            $query = "SELECT * FROM fotos_compra WHERE iphone_id = :iphone_id ORDER BY fecha_carga DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':iphone_id', $iphone_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener fotos de compra: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener fotos de venta
     */
    public function obtenerFotosVenta($venta_id)
    {
        try {
            $query = "SELECT * FROM fotos_venta WHERE venta_id = :venta_id ORDER BY fecha_carga DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':venta_id', $venta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener fotos de venta: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener fotos de venta por iPhone
     */
    public function obtenerFotosVentaPorIphone($venta_id, $iphone_id)
    {
        try {
            $query = "SELECT * FROM fotos_venta WHERE venta_id = :venta_id AND iphone_id = :iphone_id ORDER BY fecha_carga DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':venta_id', $venta_id);
            $stmt->bindParam(':iphone_id', $iphone_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener fotos de venta: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Eliminar foto
     */
    public function eliminarFoto($id, $tabla = 'fotos_compra')
    {
        try {
            // Obtener archivo primero
            $query = "SELECT archivo FROM " . $tabla . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $foto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($foto) {
                // Eliminar archivo del servidor
                $rutaArchivo = __DIR__ . '/../uploads/' . $foto['archivo'];
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
                
                // Eliminar registro de BD
                $query = "DELETE FROM " . $tabla . " WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                
                return ['success' => true];
            }
            
            return ['success' => false, 'message' => 'Foto no encontrada'];
        } catch (Exception $e) {
            error_log("Error al eliminar foto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar foto'];
        }
    }

    /**
     * Procesar y guardar archivo de foto
     */
    public static function procesarFoto($archivo, $carpeta = 'fotos')
    {
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            $errores = [
                UPLOAD_ERR_INI_SIZE => 'Archivo mayor que upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'Archivo mayor que max_file_size del formulario',
                UPLOAD_ERR_PARTIAL => 'Archivo cargado parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se cargó ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
                UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en disco'
            ];
            $msg = $errores[$archivo['error']] ?? 'Error desconocido al cargar';
            error_log("Error upload: $msg (código: {$archivo['error']})");
            return ['success' => false, 'message' => $msg];
        }
        
        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            error_log("Tipo no permitido: {$archivo['type']}");
            return ['success' => false, 'message' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP'];
        }
        
        // Validar tamaño (máx 5MB)
        $maxSize = 5 * 1024 * 1024;
        if ($archivo['size'] > $maxSize) {
            error_log("Archivo muy grande: {$archivo['size']} bytes");
            return ['success' => false, 'message' => 'El archivo es demasiado grande (máximo 5MB)'];
        }
        
        // Crear directorio si no existe
        $rutaCarpeta = __DIR__ . '/../uploads/' . $carpeta;
        if (!is_dir($rutaCarpeta)) {
            if (!@mkdir($rutaCarpeta, 0755, true)) {
                error_log("No se pudo crear directorio: $rutaCarpeta");
                return ['success' => false, 'message' => 'Error al crear directorio de destino'];
            }
        }
        
        // Verificar que el directorio es escribible
        if (!is_writable($rutaCarpeta)) {
            error_log("Directorio no es escribible: $rutaCarpeta");
            return ['success' => false, 'message' => 'Directorio de destino no tiene permisos de escritura'];
        }
        
        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = $carpeta . '/' . uniqid('foto_') . '.' . $extension;
        $rutaCompleta = __DIR__ . '/../uploads/' . $nombreArchivo;
        
        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            chmod($rutaCompleta, 0644);
            return ['success' => true, 'archivo' => $nombreArchivo];
        }
        
        error_log("No se pudo mover archivo desde: {$archivo['tmp_name']} a: $rutaCompleta");
        return ['success' => false, 'message' => 'Error al guardar el archivo en el servidor'];
    }
}
