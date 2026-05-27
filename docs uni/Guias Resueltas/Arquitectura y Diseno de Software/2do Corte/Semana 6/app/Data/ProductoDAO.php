<?php
namespace Data;

require_once __DIR__ . '/../../../../../../../config/database.php';

class ProductoDAO {
    private $db;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function registrar(\Core\Producto $producto) {
        try {
            $query = "INSERT INTO iphones (modelo, capacidad, color, condicion, imei, precio_compra, precio_venta, estado)
                      VALUES (:modelo, :capacidad, :color, :condicion, :imei, :precio_compra, :precio_venta, 'disponible')";

            $stmt = $this->db->prepare($query);
            $data = $producto->toArray();

            $stmt->bindParam(':modelo', $data['modelo']);
            $stmt->bindParam(':capacidad', $data['capacidad']);
            $stmt->bindParam(':color', $data['color']);
            $stmt->bindParam(':condicion', $data['condicion']);
            $stmt->bindParam(':imei', $data['imei']);
            $stmt->bindParam(':precio_compra', $data['precio_compra']);
            $stmt->bindParam(':precio_venta', $data['precio_venta']);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error al registrar producto: " . $e->getMessage());
            return false;
        }
    }

    public function listar() {
        $query = "SELECT id, modelo, capacidad, color, imei, precio_venta, estado
                  FROM iphones ORDER BY id DESC LIMIT 20";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
