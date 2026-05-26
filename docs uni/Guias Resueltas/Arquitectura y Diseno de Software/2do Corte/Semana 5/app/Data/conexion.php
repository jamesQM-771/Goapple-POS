<?php
require_once __DIR__ . '/../../../../../../../config/database.php';

class ConexionDB {
    private static $instancia = null;
    private $pdo;

    private function __construct() {
        try {
            $db = Database::getInstance();
            $this->pdo = $db->getConnection();
        } catch(Exception $e) {
            die("Error Fatal Conexión: " . $e->getMessage());
        }
    }

    public static function obtenerInstancia() {
        if (self::$instancia == null) {
            self::$instancia = new ConexionDB();
        }
        return self::$instancia;
    }

    public function getConnection() { return $this->pdo; }
}
