<?php
/**
 * Modelo Cliente
 * Gestión de clientes
 */

require_once __DIR__ . '/../config/database.php';

class Cliente {
    private $conn;
    private $table = 'clientes';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todos los clientes
     */
    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . $filtros['buscar'] . '%';
            $where[] = "(nombre LIKE ? OR cedula LIKE ? OR telefono LIKE ? OR email LIKE ?)";
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
     * Obtener cliente por ID
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
     * Obtener cliente por cédula
     */
    public function obtenerPorCedula($cedula) {
        $query = "SELECT * FROM " . $this->table . " WHERE cedula = :cedula LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Crear nuevo cliente
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, cedula, telefono, email, direccion, ciudad, estado, limite_credito, credito_disponible, notas) 
                  VALUES (:nombre, :cedula, :telefono, :email, :direccion, :ciudad, :estado, :limite_credito, :credito_disponible, :notas)";

        $stmt = $this->conn->prepare($query);

        $estado = $datos['estado'] ?? 'activo';
        $limite_credito = $datos['limite_credito'] ?? 0;
        $credito_disponible = $datos['credito_disponible'] ?? $limite_credito;

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':cedula', $datos['cedula']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':ciudad', $datos['ciudad']);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':limite_credito', $limite_credito);
        $stmt->bindParam(':credito_disponible', $credito_disponible);
        $stmt->bindParam(':notas', $datos['notas']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Actualizar cliente
     */
    public function actualizar($id, $datos) {
        $fields = [];
        $params = [':id' => $id];

        $camposPermitidos = ['nombre', 'cedula', 'telefono', 'email', 'direccion', 'ciudad', 'estado', 'limite_credito', 'credito_disponible', 'notas'];

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
     * Eliminar cliente
     * @param int $id ID del cliente
     * @param bool $forzar Forzar eliminación aunque tenga ventas (solo admin)
     */
    public function eliminar($id, $forzar = false) {
        if (!$forzar) {
            // Verificar ventas
            $query = "SELECT COUNT(*) as total FROM ventas WHERE cliente_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result['total'] > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar el cliente porque tiene ventas registradas'];
            }
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Cliente eliminado exitosamente'];
        }
        return ['success' => false, 'message' => 'Error al eliminar el cliente'];
    }

    /**
     * Verificar si la cédula ya existe
     */
    public function cedulaExiste($cedula, $excluir_id = null) {
        $query = "SELECT id FROM " . $this->table . " WHERE cedula = :cedula";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula', $cedula);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtener historial de compras del cliente
     */
    public function obtenerHistorialCompras($cliente_id) {
        $query = "SELECT v.*, u.nombre as vendedor
                  FROM ventas v
                  LEFT JOIN usuarios u ON v.vendedor_id = u.id
                  WHERE v.cliente_id = :cliente_id
                  ORDER BY v.fecha_venta DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener créditos del cliente
     */
    public function obtenerCreditos($cliente_id) {
        $query = "SELECT * FROM creditos WHERE cliente_id = :cliente_id ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener estadísticas del cliente
     */
    public function obtenerEstadisticas($cliente_id) {
        $query = "SELECT 
                    c.*,
                    COUNT(DISTINCT v.id) as total_ventas,
                    COALESCE(SUM(v.total), 0) as total_gastado,
                    COUNT(DISTINCT cr.id) as total_creditos,
                    COALESCE(SUM(CASE WHEN cr.estado = 'activo' THEN cr.saldo_pendiente ELSE 0 END), 0) as deuda_actual
                  FROM clientes c
                  LEFT JOIN ventas v ON c.id = v.cliente_id
                  LEFT JOIN creditos cr ON c.id = cr.cliente_id
                  WHERE c.id = :cliente_id
                  GROUP BY c.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener estadísticas generales de clientes
     */
    public function obtenerEstadisticasGenerales() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'moroso' THEN 1 ELSE 0 END) as morosos,
                    SUM(CASE WHEN estado = 'bloqueado' THEN 1 ELSE 0 END) as bloqueados,
                    SUM(total_compras) as total_ventas_general
                  FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Actualizar estado del cliente según créditos
     */
    public function actualizarEstadoPorCreditos($cliente_id) {
        $query = "SELECT COUNT(*) as creditos_mora 
                  FROM creditos 
                  WHERE cliente_id = :cliente_id AND estado = 'mora'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        $result = $stmt->fetch();

        $nuevo_estado = $result['creditos_mora'] > 0 ? 'moroso' : 'activo';

        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':id', $cliente_id);
        return $stmt->execute();
    }
}
