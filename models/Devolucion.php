<?php
/**
 * Modelo Devolucion
 * Gestión de devoluciones y cambios
 */

require_once __DIR__ . '/../config/database.php';

class Devolucion {
    private $conn;
    private $table = 'devoluciones';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todas las devoluciones
     */
    public function obtenerTodas($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = "d.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['cliente_id'])) {
            $where[] = "d.cliente_id = ?";
            $params[] = $filtros['cliente_id'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = "DATE(d.fecha_solicitud) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "DATE(d.fecha_solicitud) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $query = "SELECT d.*, 
                         c.nombre as cliente_nombre, 
                         v.numero_venta,
                         u1.nombre as usuario_solicita_nombre,
                         u2.nombre as usuario_aprueba_nombre
                  FROM " . $this->table . " d
                  INNER JOIN clientes c ON d.cliente_id = c.id
                  INNER JOIN ventas v ON d.venta_id = v.id
                  INNER JOIN usuarios u1 ON d.usuario_solicita = u1.id
                  LEFT JOIN usuarios u2 ON d.usuario_aprueba = u2.id
                  $whereClause
                  ORDER BY d.fecha_solicitud DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener devolución por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT d.*, 
                         c.nombre as cliente_nombre, 
                         c.cedula as cliente_cedula,
                         v.numero_venta,
                         v.total as venta_total,
                         v.tipo_venta,
                         v.estado as venta_estado,
                         u1.nombre as usuario_solicita_nombre,
                         u2.nombre as usuario_aprueba_nombre
                  FROM " . $this->table . " d
                  INNER JOIN clientes c ON d.cliente_id = c.id
                  INNER JOIN ventas v ON d.venta_id = v.id
                  INNER JOIN usuarios u1 ON d.usuario_solicita = u1.id
                  LEFT JOIN usuarios u2 ON d.usuario_aprueba = u2.id
                  WHERE d.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Crear nueva devolución
     */
    public function crear($datos) {
        // Generar número de devolución
        $numero = $this->generarNumeroDevolucion();

        $query = "INSERT INTO " . $this->table . " 
              (numero_devolucion, venta_id, cliente_id, motivo, descripcion, tipo_solicitud,
               iphone_original_id, iphone_nuevo_id, monto_reembolso, metodo_reembolso, usuario_solicita)
              VALUES (:numero, :venta_id, :cliente_id, :motivo, :descripcion, :tipo_solicitud,
                  :iphone_original_id, :iphone_nuevo_id, :monto_reembolso, :metodo_reembolso, :usuario_solicita)";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':venta_id', $datos['venta_id']);
        $stmt->bindParam(':cliente_id', $datos['cliente_id']);
        $stmt->bindParam(':motivo', $datos['motivo']);
        $stmt->bindParam(':descripcion', $datos['descripcion'] ?? null);
        $tipo_solicitud = $datos['tipo_solicitud'] ?? 'devolucion';
        $iphone_original_id = $datos['iphone_original_id'] ?? null;
        $iphone_nuevo_id = $datos['iphone_nuevo_id'] ?? null;
        $stmt->bindParam(':tipo_solicitud', $tipo_solicitud);
        $stmt->bindParam(':iphone_original_id', $iphone_original_id);
        $stmt->bindParam(':iphone_nuevo_id', $iphone_nuevo_id);
        $stmt->bindParam(':monto_reembolso', $datos['monto_reembolso']);
        $stmt->bindParam(':metodo_reembolso', $datos['metodo_reembolso'] ?? 'efectivo');
        $stmt->bindParam(':usuario_solicita', $datos['usuario_solicita']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Actualizar devolución
     */
    public function actualizar($id, $datos) {
        $updates = [];
        $params = [];

        foreach ($datos as $campo => $valor) {
            if (in_array($campo, ['motivo', 'descripcion', 'tipo_solicitud', 'iphone_original_id', 'iphone_nuevo_id', 'monto_reembolso', 'metodo_reembolso', 'notas'])) {
                $updates[] = "$campo = ?";
                $params[] = $valor;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $id;

        $query = "UPDATE " . $this->table . " 
                  SET " . implode(", ", $updates) . " 
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Aprobar devolución
     */
    public function aprobar($id, $usuario_aprueba) {
        $query = "UPDATE " . $this->table . " 
                  SET estado = 'aprobada', 
                      usuario_aprueba = :usuario_aprueba,
                      fecha_aprobacion = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_aprueba', $usuario_aprueba);

        return $stmt->execute();
    }

    /**
     * Rechazar devolución
     */
    public function rechazar($id, $usuario_aprueba, $motivo = '') {
        $query = "UPDATE " . $this->table . " 
                  SET estado = 'rechazada', 
                      usuario_aprueba = :usuario_aprueba,
                      fecha_aprobacion = NOW(),
                      notas = ?
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_aprueba', $usuario_aprueba);
        $stmt->bindValue(1, $motivo);

        return $stmt->execute();
    }

    /**
     * Completar devolución
     */
    public function completar($id, $usuario_aprueba = null) {
        try {
            $this->conn->beginTransaction();

            $devolucion = $this->obtenerPorId($id);
            if (!$devolucion) {
                throw new Exception('Devolución no encontrada');
            }

            if ($devolucion['estado'] !== 'aprobada') {
                throw new Exception('Solo se pueden completar devoluciones aprobadas');
            }

            if (($devolucion['tipo_solicitud'] ?? 'devolucion') === 'cambio') {
                if (empty($devolucion['iphone_original_id']) || empty($devolucion['iphone_nuevo_id'])) {
                    throw new Exception('Faltan iPhones para completar el cambio');
                }

                // Devolver iPhone original al inventario
                $query_update_original = "UPDATE iphones SET estado = 'disponible', fecha_venta = NULL WHERE id = :iphone_id";
                $stmt_update_original = $this->conn->prepare($query_update_original);
                $stmt_update_original->bindParam(':iphone_id', $devolucion['iphone_original_id']);
                $stmt_update_original->execute();

                // Asignar iPhone nuevo a la venta
                $nuevo_estado = $devolucion['tipo_venta'] === 'credito' ? 'en_credito' : 'vendido';
                $query_update_nuevo = "UPDATE iphones SET estado = :estado, fecha_venta = NOW() WHERE id = :iphone_id";
                $stmt_update_nuevo = $this->conn->prepare($query_update_nuevo);
                $stmt_update_nuevo->bindParam(':estado', $nuevo_estado);
                $stmt_update_nuevo->bindParam(':iphone_id', $devolucion['iphone_nuevo_id']);
                $stmt_update_nuevo->execute();

                // Actualizar detalle de venta reemplazando el iPhone original
                $query_update_detalle = "UPDATE detalle_ventas 
                                        SET iphone_id = :iphone_nuevo_id
                                        WHERE venta_id = :venta_id AND iphone_id = :iphone_original_id
                                        LIMIT 1";
                $stmt_update_detalle = $this->conn->prepare($query_update_detalle);
                $stmt_update_detalle->bindParam(':iphone_nuevo_id', $devolucion['iphone_nuevo_id']);
                $stmt_update_detalle->bindParam(':venta_id', $devolucion['venta_id']);
                $stmt_update_detalle->bindParam(':iphone_original_id', $devolucion['iphone_original_id']);
                $stmt_update_detalle->execute();
            } else {
                // Revertir inventario de iPhones asociados a la venta
                $query_items = "SELECT iphone_id FROM detalle_ventas WHERE venta_id = :venta_id";
                $stmt_items = $this->conn->prepare($query_items);
                $stmt_items->bindParam(':venta_id', $devolucion['venta_id']);
                $stmt_items->execute();
                $items = $stmt_items->fetchAll();

                foreach ($items as $item) {
                    $query_update_iphone = "UPDATE iphones SET estado = 'disponible', fecha_venta = NULL WHERE id = :iphone_id";
                    $stmt_update_iphone = $this->conn->prepare($query_update_iphone);
                    $stmt_update_iphone->bindParam(':iphone_id', $item['iphone_id']);
                    $stmt_update_iphone->execute();
                }

                // Ajustar venta original
                $query_venta = "UPDATE ventas SET estado = 'cancelada' WHERE id = :venta_id";
                $stmt_venta = $this->conn->prepare($query_venta);
                $stmt_venta->bindParam(':venta_id', $devolucion['venta_id']);
                $stmt_venta->execute();

                // Ajustar crédito si la venta fue a crédito
                if ($devolucion['tipo_venta'] === 'credito') {
                    $query_credito = "SELECT id, saldo_pendiente FROM creditos WHERE venta_id = :venta_id LIMIT 1";
                    $stmt_credito = $this->conn->prepare($query_credito);
                    $stmt_credito->bindParam(':venta_id', $devolucion['venta_id']);
                    $stmt_credito->execute();
                    $credito = $stmt_credito->fetch();

                    if ($credito) {
                        $nuevo_saldo = max(0, floatval($credito['saldo_pendiente']) - floatval($devolucion['monto_reembolso']));
                        $nuevo_estado = $nuevo_saldo <= 0 ? 'pagado' : 'activo';

                        $query_update_credito = "UPDATE creditos 
                                                SET saldo_pendiente = :saldo_pendiente, estado = :estado
                                                WHERE id = :credito_id";
                        $stmt_update_credito = $this->conn->prepare($query_update_credito);
                        $stmt_update_credito->bindParam(':saldo_pendiente', $nuevo_saldo);
                        $stmt_update_credito->bindParam(':estado', $nuevo_estado);
                        $stmt_update_credito->bindParam(':credito_id', $credito['id']);
                        $stmt_update_credito->execute();
                    }
                }
            }

            // Completar devolución
            $query = "UPDATE " . $this->table . " 
                      SET estado = 'completada'
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error al completar devolución: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener devoluciones pendientes
     */
    public function obtenerPendientes() {
        $query = "SELECT d.*, 
                         c.nombre as cliente_nombre,
                         v.numero_venta
                  FROM " . $this->table . " d
                  INNER JOIN clientes c ON d.cliente_id = c.id
                  INNER JOIN ventas v ON d.venta_id = v.id
                  WHERE d.estado = 'pendiente'
                  ORDER BY d.fecha_solicitud DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Obtener estadísticas de devoluciones por rango
     */
    public function obtenerEstadisticas($fecha_desde = null, $fecha_hasta = null) {
        $where = [];
        $params = [];

        if (!empty($fecha_desde)) {
            $where[] = "DATE(d.fecha_solicitud) >= ?";
            $params[] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $where[] = "DATE(d.fecha_solicitud) <= ?";
            $params[] = $fecha_hasta;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT 
                    COUNT(*) as total_devoluciones,
                    COALESCE(SUM(CASE WHEN d.tipo_solicitud = 'devolucion' THEN d.monto_reembolso ELSE 0 END), 0) as total_reembolsado,
                    SUM(CASE WHEN d.tipo_solicitud = 'cambio' THEN 1 ELSE 0 END) as total_cambios,
                    SUM(CASE WHEN d.estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN d.estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN d.estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas,
                    SUM(CASE WHEN d.estado = 'completada' THEN 1 ELSE 0 END) as completadas
                  FROM " . $this->table . " d
                  $whereClause";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Generar número de devolución secuencial
     */
    private function generarNumeroDevolucion() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        $numero = ($result['total'] + 1);
        $año = date('Y');
        
        return "DEV-" . $año . "-" . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Eliminar devolución
     */
    public function eliminar($id) {
        // Solo se pueden eliminar devoluciones pendientes
        $query = "DELETE FROM " . $this->table . " 
                  WHERE id = :id AND estado = 'pendiente'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}
?>
