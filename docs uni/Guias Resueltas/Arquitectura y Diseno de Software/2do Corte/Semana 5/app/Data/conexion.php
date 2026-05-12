<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: Giorgi Julian Ordoñez | Guía: 5 */
class ConexionDB {
    private static $instancia = null;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=goapple_db", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
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
?>