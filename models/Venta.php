<?php
/**
 * Modelo Venta
 * Gestión de ventas
 */

require_once __DIR__ . '/../config/database.php';

class Venta {
    private $conn;
    private $table = 'ventas';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todas las ventas
     */
    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . $filtros['buscar'] . '%';
            $where[] = "(v.numero_venta LIKE ? OR c.nombre LIKE ? OR c.cedula LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
        }

        if (!empty($filtros['tipo_venta'])) {
            $where[] = "v.tipo_venta = ?";
            $params[] = $filtros['tipo_venta'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = "v.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = "DATE(v.fecha_venta) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "DATE(v.fecha_venta) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $query = "SELECT v.*, 
                         c.nombre as cliente_nombre, c.cedula as cliente_cedula,
                         u.nombre as vendedor_nombre
                  FROM " . $this->table . " v
                  INNER JOIN clientes c ON v.cliente_id = c.id
                  INNER JOIN usuarios u ON v.vendedor_id = u.id
                  $whereClause 
                  ORDER BY v.fecha_venta DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener venta por ID

    /**
     * Obtener venta por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT v.*, 
                         c.nombre as cliente_nombre, c.cedula as cliente_cedula, 
                         c.telefono as cliente_telefono, c.direccion as cliente_direccion,
                         u.nombre as vendedor_nombre
                  FROM " . $this->table . " v
                  INNER JOIN clientes c ON v.cliente_id = c.id
                  INNER JOIN usuarios u ON v.vendedor_id = u.id
                  WHERE v.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Obtener venta por número
     */
    public function obtenerPorNumero($numero_venta) {
        $query = "SELECT v.*, 
                         c.nombre as cliente_nombre, c.cedula as cliente_cedula,
                         u.nombre as vendedor_nombre
                  FROM " . $this->table . " v
                  INNER JOIN clientes c ON v.cliente_id = c.id
                  INNER JOIN usuarios u ON v.vendedor_id = u.id
                  WHERE v.numero_venta = :numero_venta LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':numero_venta', $numero_venta);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Crear nueva venta (con transacción)
     */
    public function crear($datos, $productos) {
        try {
            $this->conn->beginTransaction();

            // Generar número de venta
            $numero_venta = $this->generarNumeroVenta();

            // Insertar venta
            $query = "INSERT INTO " . $this->table . " 
                      (numero_venta, cliente_id, vendedor_id, tipo_venta, subtotal, descuento, total, forma_pago, estado, observaciones) 
                      VALUES (:numero_venta, :cliente_id, :vendedor_id, :tipo_venta, :subtotal, :descuento, :total, :forma_pago, :estado, :observaciones)";

            $stmt = $this->conn->prepare($query);

            $estado = $datos['estado'] ?? 'completada';
            $descuento = $datos['descuento'] ?? 0;

            $stmt->bindParam(':numero_venta', $numero_venta);
            $stmt->bindParam(':cliente_id', $datos['cliente_id']);
            $stmt->bindParam(':vendedor_id', $datos['vendedor_id']);
            $stmt->bindParam(':tipo_venta', $datos['tipo_venta']);
            $stmt->bindParam(':subtotal', $datos['subtotal']);
            $stmt->bindParam(':descuento', $descuento);
            $stmt->bindParam(':total', $datos['total']);
            $stmt->bindParam(':forma_pago', $datos['forma_pago']);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':observaciones', $datos['observaciones']);

            $stmt->execute();
            $venta_id = $this->conn->lastInsertId();

            // Insertar detalle de venta
            $query_detalle = "INSERT INTO detalle_ventas (venta_id, iphone_id, precio_unitario, cantidad, subtotal) 
                              VALUES (:venta_id, :iphone_id, :precio_unitario, :cantidad, :subtotal)";
            $stmt_detalle = $this->conn->prepare($query_detalle);

            foreach ($productos as $producto) {
                $stmt_detalle->bindParam(':venta_id', $venta_id);
                $stmt_detalle->bindParam(':iphone_id', $producto['iphone_id']);
                $stmt_detalle->bindParam(':precio_unitario', $producto['precio_unitario']);
                $cantidad = $producto['cantidad'] ?? 1;
                $stmt_detalle->bindParam(':cantidad', $cantidad);
                $stmt_detalle->bindParam(':subtotal', $producto['subtotal']);
                $stmt_detalle->execute();

                // Actualizar estado del iPhone (lo hace el trigger, pero podemos hacerlo manualmente también)
                $nuevo_estado = $datos['tipo_venta'] == 'contado' ? 'vendido' : 'en_credito';
                $query_update = "UPDATE iphones SET estado = :estado, fecha_venta = NOW() WHERE id = :id";
                $stmt_update = $this->conn->prepare($query_update);
                $stmt_update->bindParam(':estado', $nuevo_estado);
                $stmt_update->bindParam(':id', $producto['iphone_id']);
                $stmt_update->execute();
            }

            $this->conn->commit();
            return ['success' => true, 'venta_id' => $venta_id, 'numero_venta' => $numero_venta];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error al crear venta: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear la venta: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener detalle de venta
     */
    public function obtenerDetalle($venta_id) {
        $query = "SELECT dv.*, 
                         i.modelo, i.capacidad, i.color, i.imei, i.condicion,
                         p.nombre as proveedor_nombre
                  FROM detalle_ventas dv
                  INNER JOIN iphones i ON dv.iphone_id = i.id
                  LEFT JOIN proveedores p ON i.proveedor_id = p.id
                  WHERE dv.venta_id = :venta_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':venta_id', $venta_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Generar número consecutivo de venta
     */
    private function generarNumeroVenta() {
        $query = "SELECT numero_venta FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $ultimo = $stmt->fetch();
            $numero = intval(substr($ultimo['numero_venta'], 2)) + 1;
        } else {
            $numero = 1;
        }

        return 'VT' . str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Actualizar venta
     */
    public function actualizar($id, $datos) {
        try {
            $campos = [];
            $params = [':id' => $id];

            if (isset($datos['observaciones'])) {
                $campos[] = "observaciones = :observaciones";
                $params[':observaciones'] = $datos['observaciones'];
            }

            if (empty($campos)) {
                return true;
            }

            $query = "UPDATE " . $this->table . " SET " . implode(', ', $campos) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->rowCount() > 0 || true;
        } catch (Exception $e) {
            error_log("Error al actualizar venta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancelar venta
     */
    public function cancelar($id, $motivo = '') {
        try {
            $this->conn->beginTransaction();

            // Obtener productos de la venta
            $productos = $this->obtenerDetalle($id);

            // Devolver iPhones a disponible
            foreach ($productos as $producto) {
                $query = "UPDATE iphones SET estado = 'disponible', fecha_venta = NULL WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $producto['iphone_id']);
                $stmt->execute();
            }

            // Actualizar estado de venta
            $query = "UPDATE " . $this->table . " 
                      SET estado = 'cancelada', observaciones = CONCAT(observaciones, '\nCANCELADA: ', :motivo) 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':motivo', $motivo);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Venta cancelada exitosamente'];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al cancelar la venta: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener estadísticas de ventas
     */
    public function obtenerEstadisticas($fecha_desde = null, $fecha_hasta = null) {
        $where = ["estado = 'completada'"];
        $params = [];

        if ($fecha_desde) {
            $where[] = "DATE(fecha_venta) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }

        if ($fecha_hasta) {
            $where[] = "DATE(fecha_venta) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }

        $whereClause = "WHERE " . implode(" AND ", $where);

        $query = "SELECT 
                    COUNT(*) as total_ventas,
                    SUM(CASE WHEN tipo_venta = 'contado' THEN 1 ELSE 0 END) as ventas_contado,
                    SUM(CASE WHEN tipo_venta = 'credito' THEN 1 ELSE 0 END) as ventas_credito,
                    SUM(total) as total_vendido,
                    SUM(CASE WHEN tipo_venta = 'contado' THEN total ELSE 0 END) as total_contado,
                    SUM(CASE WHEN tipo_venta = 'credito' THEN total ELSE 0 END) as total_credito,
                    AVG(total) as ticket_promedio
                  FROM " . $this->table . "
                  $whereClause";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener ventas por vendedor
     */
    public function obtenerPorVendedor($vendedor_id, $fecha_desde = null, $fecha_hasta = null) {
        $where = ["v.vendedor_id = :vendedor_id", "v.estado = 'completada'"];
        $params = [':vendedor_id' => $vendedor_id];

        if ($fecha_desde) {
            $where[] = "DATE(v.fecha_venta) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }

        if ($fecha_hasta) {
            $where[] = "DATE(v.fecha_venta) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }

        $whereClause = "WHERE " . implode(" AND ", $where);

        $query = "SELECT v.*, c.nombre as cliente_nombre
                  FROM " . $this->table . " v
                  INNER JOIN clientes c ON v.cliente_id = c.id
                  $whereClause
                  ORDER BY v.fecha_venta DESC";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener ranking de vendedores
     */
    public function obtenerRankingVendedores($fecha_desde = null, $fecha_hasta = null) {
        $where = ["v.estado = 'completada'"];
        $params = [];

        if ($fecha_desde) {
            $where[] = "DATE(v.fecha_venta) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }

        if ($fecha_hasta) {
            $where[] = "DATE(v.fecha_venta) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }

        $whereClause = "WHERE " . implode(" AND ", $where);

        $query = "SELECT 
                    u.id, u.nombre,
                    COUNT(v.id) as total_ventas,
                    SUM(v.total) as total_vendido,
                    AVG(v.total) as ticket_promedio
                  FROM usuarios u
                  INNER JOIN ventas v ON u.id = v.vendedor_id
                  $whereClause
                  GROUP BY u.id, u.nombre
                  ORDER BY total_vendido DESC";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }
}
