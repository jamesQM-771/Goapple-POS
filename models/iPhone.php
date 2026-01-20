<?php
/**
 * Modelo iPhone
 * Gestión de inventario de iPhones
 */

require_once __DIR__ . '/../config/database.php';

class iPhone {
    private $conn;
    private $table = 'iphones';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todos los iPhones
     */
    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . $filtros['buscar'] . '%';
            $where[] = "(modelo LIKE ? OR imei LIKE ? OR color LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
        }

        if (!empty($filtros['estado'])) {
            $where[] = "i.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['condicion'])) {
            $where[] = "i.condicion = ?";
            $params[] = $filtros['condicion'];
        }

        if (!empty($filtros['proveedor_id'])) {
            $where[] = "i.proveedor_id = ?";
            $params[] = $filtros['proveedor_id'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $query = "SELECT i.*, p.nombre as proveedor_nombre, p.empresa as proveedor_empresa 
                  FROM " . $this->table . " i
                  LEFT JOIN proveedores p ON i.proveedor_id = p.id
                  $whereClause 
                  ORDER BY i.fecha_ingreso DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener iPhone por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT i.*, p.nombre as proveedor_nombre, p.empresa as proveedor_empresa 
                  FROM " . $this->table . " i
                  LEFT JOIN proveedores p ON i.proveedor_id = p.id
                  WHERE i.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Obtener iPhone por IMEI
     */
    public function obtenerPorIMEI($imei) {
        $query = "SELECT * FROM " . $this->table . " WHERE imei = :imei LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':imei', $imei);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Crear nuevo iPhone
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (modelo, capacidad, color, condicion, estado_bateria, imei, proveedor_id, 
                   precio_compra, precio_venta, estado, observaciones) 
                  VALUES (:modelo, :capacidad, :color, :condicion, :estado_bateria, :imei, :proveedor_id, 
                          :precio_compra, :precio_venta, :estado, :observaciones)";

        $stmt = $this->conn->prepare($query);

        $estado = $datos['estado'] ?? 'disponible';
        $estado_bateria = $datos['estado_bateria'] ?? 100;

        $stmt->bindParam(':modelo', $datos['modelo']);
        $stmt->bindParam(':capacidad', $datos['capacidad']);
        $stmt->bindParam(':color', $datos['color']);
        $stmt->bindParam(':condicion', $datos['condicion']);
        $stmt->bindParam(':estado_bateria', $estado_bateria);
        $stmt->bindParam(':imei', $datos['imei']);
        $stmt->bindParam(':proveedor_id', $datos['proveedor_id']);
        $stmt->bindParam(':precio_compra', $datos['precio_compra']);
        $stmt->bindParam(':precio_venta', $datos['precio_venta']);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':observaciones', $datos['observaciones']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Actualizar iPhone
     */
    public function actualizar($id, $datos) {
        $fields = [];
        $params = [':id' => $id];

        $camposPermitidos = ['modelo', 'capacidad', 'color', 'condicion', 'estado_bateria', 'imei', 
                             'proveedor_id', 'precio_compra', 'precio_venta', 'estado', 'observaciones'];

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
     * Eliminar iPhone
     */
    public function eliminar($id) {
        // Verificar que no esté vendido o en crédito
        $query = "SELECT estado FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result && in_array($result['estado'], ['vendido', 'en_credito'])) {
            return ['success' => false, 'message' => 'No se puede eliminar un iPhone que ha sido vendido o está en crédito'];
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'iPhone eliminado exitosamente'];
        }
        return ['success' => false, 'message' => 'Error al eliminar el iPhone'];
    }

    /**
     * Verificar si el IMEI ya existe
     */
    public function imeiExiste($imei, $excluir_id = null) {
        $query = "SELECT id FROM " . $this->table . " WHERE imei = :imei";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':imei', $imei);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtener iPhones disponibles para venta
     */
    public function obtenerDisponibles() {
        $query = "SELECT i.*, p.nombre as proveedor_nombre 
                  FROM " . $this->table . " i
                  LEFT JOIN proveedores p ON i.proveedor_id = p.id
                  WHERE i.estado = 'disponible' 
                  ORDER BY i.fecha_ingreso DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Actualizar estado del iPhone
     */
    public function actualizarEstado($id, $estado) {
        $query = "UPDATE " . $this->table . " 
                  SET estado = :estado, fecha_venta = " . ($estado != 'disponible' ? "NOW()" : "NULL") . "
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Obtener estadísticas de inventario
     */
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN estado = 'vendido' THEN 1 ELSE 0 END) as vendidos,
                    SUM(CASE WHEN estado = 'en_credito' THEN 1 ELSE 0 END) as en_credito,
                    SUM(CASE WHEN estado = 'apartado' THEN 1 ELSE 0 END) as apartados,
                    SUM(CASE WHEN condicion = 'nuevo' THEN 1 ELSE 0 END) as nuevos,
                    SUM(CASE WHEN condicion = 'usado' THEN 1 ELSE 0 END) as usados,
                    SUM(CASE WHEN estado = 'disponible' THEN precio_compra ELSE 0 END) as valor_inventario_compra,
                    SUM(CASE WHEN estado = 'disponible' THEN precio_venta ELSE 0 END) as valor_inventario_venta
                  FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener modelos más vendidos
     */
    public function obtenerModelosMasVendidos($limite = 10) {
        $query = "SELECT modelo, capacidad, COUNT(*) as total_vendidos
                  FROM " . $this->table . "
                  WHERE estado IN ('vendido', 'en_credito')
                  GROUP BY modelo, capacidad
                  ORDER BY total_vendidos DESC
                  LIMIT :limite";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener alertas de stock bajo (modelos con menos de X unidades disponibles)
     */
    public function obtenerAlertasStock($minimo = 3) {
        $query = "SELECT modelo, capacidad, COUNT(*) as cantidad_disponible
                  FROM " . $this->table . "
                  WHERE estado = 'disponible'
                  GROUP BY modelo, capacidad
                  HAVING cantidad_disponible < :minimo
                  ORDER BY cantidad_disponible ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':minimo', $minimo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
