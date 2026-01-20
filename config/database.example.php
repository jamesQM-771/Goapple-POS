<?php
/**
 * Configuración de la base de datos - EJEMPLO
 * Copiar este archivo como database.php y configurar con tus credenciales
 * Sistema POS GOapple
 */

class Database {
    // Configuración para localhost/XAMPP
    private $host = "localhost";
    private $db_name = "goapple_pos";
    private $username = "root";
    private $password = "";
    
    // Configuración para producción (comentar localhost y descomentar estas líneas)
    // private $host = "tu_servidor.com";
    // private $db_name = "tu_base_datos";
    // private $username = "tu_usuario";
    // private $password = "tu_contraseña";
    
    private $charset = "utf8mb4";
    public $conn;

    /**
     * Obtener conexión a la base de datos
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            // En producción, no mostrar detalles del error
            error_log("Error de conexión BD: " . $e->getMessage());
            die("Error al conectar con la base de datos. Por favor contacte al administrador.");
        }

        return $this->conn;
    }
}
