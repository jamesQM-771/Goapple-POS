<?php
/**
 * Modelo Configuracion
 * Gestión de configuraciones del sistema
 */

require_once __DIR__ . '/../config/database.php';

class Configuracion {
    private $conn;
    private $table = 'configuracion';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener valor de configuración por clave
     */
    public function obtener($clave) {
        $query = "SELECT valor FROM " . $this->table . " WHERE clave = :clave LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            return $row['valor'];
        }
        
        return null;
    }

    /**
     * Obtener todas las configuraciones
     */
    public function obtenerTodas() {
        $query = "SELECT clave, valor FROM " . $this->table . " ORDER BY clave ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $config = [];
        while ($row = $stmt->fetch()) {
            $config[$row['clave']] = $row['valor'];
        }

        return $config;
    }

    /**
     * Actualizar o crear configuración
     */
    public function actualizar($clave, $valor) {
        // Primero verificar si existe
        $query = "SELECT id FROM " . $this->table . " WHERE clave = :clave LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Actualizar
            $query = "UPDATE " . $this->table . " SET valor = :valor WHERE clave = :clave";
        } else {
            // Crear
            $query = "INSERT INTO " . $this->table . " (clave, valor) VALUES (:clave, :valor)";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->bindParam(':valor', $valor);

        return $stmt->execute();
    }

    /**
     * Obtener grupo de configuraciones
     */
    public function obtenerGrupo($prefijo) {
        $query = "SELECT clave, valor FROM " . $this->table . " 
                  WHERE clave LIKE :prefijo 
                  ORDER BY clave ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':prefijo', $prefijo . '%');
        $stmt->execute();

        $config = [];
        while ($row = $stmt->fetch()) {
            $config[$row['clave']] = $row['valor'];
        }

        return $config;
    }
}
?>
