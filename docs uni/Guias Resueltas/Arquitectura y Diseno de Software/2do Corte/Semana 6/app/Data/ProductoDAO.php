<?php
namespace Data;
require_once __DIR__ . '/../../Semana 5/app/Data/conexion.php';

class ProductoDAO {
    private $db;
    public function __construct() {
        $this->db = \ConexionDB::obtenerInstancia()->getConnection();
    }
    public function registrar(\Core\Producto $producto) {
        // Logica DAO simulada
        return true;
    }
}
?>