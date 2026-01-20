<?php
/**
 * Configuración de Conexión a Base de Datos
 * Sistema POS GoApple
 * Usa patrón Singleton para evitar múltiples conexiones
 */

class Database {
    private static $instance = null;
    private $conn;
    
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;

    private function __construct() {
        $env = require __DIR__ . '/env.php';
        
        $this->host = $env['DB_HOST'];
        $this->db_name = $env['DB_NAME'];
        $this->username = $env['DB_USER'];
        $this->password = $env['DB_PASS'];
        $this->charset = $env['DB_CHARSET'];
        
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
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
            error_log("Error de conexión: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor contacte al administrador.");
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }

    private function __clone() {}

    public function __wakeup() {
        throw new Exception("No se puede deserializar singleton");
    }
}
