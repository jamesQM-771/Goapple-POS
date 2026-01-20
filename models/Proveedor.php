<?php
/**
 * Modelo Proveedor
 * Gestión de proveedores
 */

require_once __DIR__ . '/../config/database.php';

class Proveedor {
    private $conn;
    private $table = 'proveedores';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todos los proveedores
     */
    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . $filtros['buscar'] . '%';
            $where[] = "(nombre LIKE ? OR empresa LIKE ? OR nit_cedula LIKE ? OR telefono LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
        }

        if (!empty($filtros['estado'])) {
            $where[] = "estado = ?";
            $params[] = $filtros['estado'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $query = "SELECT * FROM " . $this->table . " $whereClause ORDER BY fecha_registro DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener proveedor por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Crear nuevo proveedor
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, empresa, nit_cedula, telefono, email, direccion, ciudad, pais, estado) 
                  VALUES (:nombre, :empresa, :nit_cedula, :telefono, :email, :direccion, :ciudad, :pais, :estado)";

        $stmt = $this->conn->prepare($query);

        $pais = $datos['pais'] ?? 'Colombia';
        $estado = $datos['estado'] ?? 'activo';

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':empresa', $datos['empresa']);
        $stmt->bindParam(':nit_cedula', $datos['nit_cedula']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':ciudad', $datos['ciudad']);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':estado', $estado);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Actualizar proveedor
     */
    public function actualizar($id, $datos) {
        $fields = [];
        $params = [':id' => $id];

        $camposPermitidos = ['nombre', 'empresa', 'nit_cedula', 'telefono', 'email', 'direccion', 'ciudad', 'pais', 'estado'];

        foreach ($camposPermitidos as $campo) {
            if (isset($datos[$campo])) {
                $fields[] = "$campo = :$campo";
                $params[":$campo"] = $datos[$campo];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }

    /**
     * Eliminar proveedor
     */
    public function eliminar($id) {
        // Verificar que no tenga iPhones asociados
        $query = "SELECT COUNT(*) as total FROM iphones WHERE proveedor_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result['total'] > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar el proveedor porque tiene productos asociados'];
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Proveedor eliminado exitosamente'];
        }
        return ['success' => false, 'message' => 'Error al eliminar el proveedor'];
    }

    /**
     * Verificar si el NIT ya existe
     */
    public function nitExiste($nit_cedula, $excluir_id = null) {
        $query = "SELECT id FROM " . $this->table . " WHERE nit_cedula = :nit_cedula";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nit_cedula', $nit_cedula);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtener productos del proveedor
     */
    public function obtenerProductos($proveedor_id) {
        $query = "SELECT * FROM iphones WHERE proveedor_id = :proveedor_id ORDER BY fecha_ingreso DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proveedor_id', $proveedor_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener estadísticas del proveedor
     */
    public function obtenerEstadisticas($proveedor_id) {
        $query = "SELECT 
                    COUNT(*) as total_productos,
                    SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN estado = 'vendido' THEN 1 ELSE 0 END) as vendidos,
                    SUM(CASE WHEN estado = 'en_credito' THEN 1 ELSE 0 END) as en_credito,
                    SUM(precio_compra) as total_invertido,
                    SUM(CASE WHEN estado = 'vendido' THEN precio_venta ELSE 0 END) as total_vendido
                  FROM iphones 
                  WHERE proveedor_id = :proveedor_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':proveedor_id', $proveedor_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener estadísticas generales de proveedores
     */
    public function obtenerEstadisticasGenerales() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                  FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener proveedores para select
     */
    public function obtenerParaSelect() {
        $query = "SELECT id, nombre, empresa FROM " . $this->table . " WHERE estado = 'activo' ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
