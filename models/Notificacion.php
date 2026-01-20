<?php
/**
 * Modelo Notificacion
 * Gestión de notificaciones del sistema
 */

require_once __DIR__ . '/../config/database.php';

class Notificacion {
    private $conn;
    private $table = 'notificaciones';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Crear nueva notificación
     */
    public function crear($usuario_id, $tipo, $titulo, $mensaje, $icono = '', $enlace = '') {
        $query = "INSERT INTO " . $this->table . " 
                  (usuario_id, tipo, titulo, mensaje, icono, enlace)
                  VALUES (:usuario_id, :tipo, :titulo, :mensaje, :icono, :enlace)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':mensaje', $mensaje);
        $stmt->bindParam(':icono', $icono);
        $stmt->bindParam(':enlace', $enlace);
        
        return $stmt->execute();
    }

    /**
     * Obtener notificaciones no leídas de un usuario
     */
    public function obtenerNoLeidas($usuario_id, $limite = 10) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE usuario_id = :usuario_id AND leida = FALSE
                  ORDER BY fecha_creacion DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener todas las notificaciones de un usuario
     */
    public function obtenerTodas($usuario_id, $limite = 50) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE usuario_id = :usuario_id
                  ORDER BY fecha_creacion DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($id) {
        $query = "UPDATE " . $this->table . " 
                  SET leida = TRUE, fecha_lectura = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas($usuario_id) {
        $query = "UPDATE " . $this->table . " 
                  SET leida = TRUE, fecha_lectura = NOW()
                  WHERE usuario_id = :usuario_id AND leida = FALSE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute();
    }

    /**
     * Contar notificaciones no leídas
     */
    public function contarNoLeidas($usuario_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE usuario_id = :usuario_id AND leida = FALSE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Eliminar notificación
     */
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Limpiar notificaciones antiguas (más de 30 días)
     */
    public function limpiarAntiguos() {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>
