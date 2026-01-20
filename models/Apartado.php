<?php
/**
 * Modelo Apartado
 * Gestión de apartados y abonos
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Venta.php';

class Apartado {
    private $conn;
    private $table = 'apartados';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = "a.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['cliente_id'])) {
            $where[] = "a.cliente_id = ?";
            $params[] = $filtros['cliente_id'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = "DATE(a.fecha_apartado) >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "DATE(a.fecha_apartado) <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT a.*, c.nombre as cliente_nombre, i.modelo, i.capacidad, i.color, i.imei
                  FROM {$this->table} a
                  INNER JOIN clientes c ON a.cliente_id = c.id
                  INNER JOIN iphones i ON a.iphone_id = i.id
                  {$whereClause}
                  ORDER BY a.fecha_apartado DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $query = "SELECT a.*, c.nombre as cliente_nombre, c.cedula as cliente_cedula, c.telefono as cliente_telefono,
                         i.modelo, i.capacidad, i.color, i.imei, i.precio_venta,
                         u.nombre as vendedor_nombre
                  FROM {$this->table} a
                  INNER JOIN clientes c ON a.cliente_id = c.id
                  INNER JOIN iphones i ON a.iphone_id = i.id
                  INNER JOIN usuarios u ON a.vendedor_id = u.id
                  WHERE a.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function obtenerPagos($apartado_id) {
        $query = "SELECT p.*, u.nombre as usuario_nombre
                  FROM apartados_pagos p
                  INNER JOIN usuarios u ON p.usuario_id = u.id
                  WHERE p.apartado_id = :apartado_id
                  ORDER BY p.fecha_pago DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':apartado_id', $apartado_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function crear($datos) {
        try {
            $this->conn->beginTransaction();

            $numero = $this->generarNumeroApartado();
            $monto_total = $datos['monto_total'];
            $abono_inicial = $datos['abono_inicial'] ?? 0;
            $total_abonado = $abono_inicial;
            $saldo_pendiente = $monto_total - $total_abonado;

            $query = "INSERT INTO {$this->table}
                      (numero_apartado, cliente_id, iphone_id, vendedor_id, fecha_limite,
                       monto_total, abono_inicial, total_abonado, saldo_pendiente, estado, observaciones)
                      VALUES (:numero, :cliente_id, :iphone_id, :vendedor_id, :fecha_limite,
                              :monto_total, :abono_inicial, :total_abonado, :saldo_pendiente, :estado, :observaciones)";

            $stmt = $this->conn->prepare($query);
            $estado = 'activo';

            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':cliente_id', $datos['cliente_id']);
            $stmt->bindParam(':iphone_id', $datos['iphone_id']);
            $stmt->bindParam(':vendedor_id', $datos['vendedor_id']);
            $stmt->bindParam(':fecha_limite', $datos['fecha_limite']);
            $stmt->bindParam(':monto_total', $monto_total);
            $stmt->bindParam(':abono_inicial', $abono_inicial);
            $stmt->bindParam(':total_abonado', $total_abonado);
            $stmt->bindParam(':saldo_pendiente', $saldo_pendiente);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':observaciones', $datos['observaciones']);
            $stmt->execute();

            $apartado_id = $this->conn->lastInsertId();

            // Registrar abono inicial si existe
            if ($abono_inicial > 0) {
                $this->registrarPago($apartado_id, $abono_inicial, $datos['forma_pago'] ?? 'efectivo', $datos['vendedor_id'], 'Abono inicial', false);
            }

            // Cambiar estado del iPhone a apartado
            $query_update = "UPDATE iphones SET estado = 'apartado' WHERE id = :iphone_id";
            $stmt_update = $this->conn->prepare($query_update);
            $stmt_update->bindParam(':iphone_id', $datos['iphone_id']);
            $stmt_update->execute();

            $this->conn->commit();

            if ($saldo_pendiente <= 0) {
                $this->completarSiAplicable($apartado_id, $datos['vendedor_id']);
            }

            return $apartado_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error al crear apartado: ' . $e->getMessage());
            return false;
        }
    }

    public function registrarPago($apartado_id, $monto, $forma_pago, $usuario_id, $observaciones = '', $hacer_commit = true) {
        try {
            $apartado = $this->obtenerPorId($apartado_id);
            if (!$apartado) {
                throw new Exception('Apartado no encontrado');
            }
            if ($apartado['estado'] !== 'activo') {
                throw new Exception('El apartado no está activo');
            }
            if ($monto <= 0) {
                throw new Exception('El monto debe ser mayor a 0');
            }
            if ($monto > $apartado['saldo_pendiente']) {
                throw new Exception('El monto excede el saldo pendiente');
            }

            if ($hacer_commit) {
                $this->conn->beginTransaction();
            }

            $query = "INSERT INTO apartados_pagos (apartado_id, monto, forma_pago, usuario_id, observaciones)
                      VALUES (:apartado_id, :monto, :forma_pago, :usuario_id, :observaciones)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':apartado_id', $apartado_id);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':forma_pago', $forma_pago);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->execute();

            // Actualizar totales
            $query_update = "UPDATE {$this->table}
                            SET total_abonado = total_abonado + :monto,
                                saldo_pendiente = monto_total - (total_abonado + :monto)
                            WHERE id = :id";
            $stmt_update = $this->conn->prepare($query_update);
            $stmt_update->bindParam(':monto', $monto);
            $stmt_update->bindParam(':id', $apartado_id);
            $stmt_update->execute();

            if ($hacer_commit) {
                $this->conn->commit();
            }
            return true;
        } catch (Exception $e) {
            if ($hacer_commit) {
                $this->conn->rollBack();
            }
            error_log('Error al registrar pago de apartado: ' . $e->getMessage());
            return false;
        }
    }

    public function completarSiAplicable($apartado_id, $usuario_id) {
        $apartado = $this->obtenerPorId($apartado_id);
        if (!$apartado) {
            return ['success' => false, 'message' => 'Apartado no encontrado'];
        }

        if ($apartado['saldo_pendiente'] > 0) {
            return ['success' => false, 'message' => 'Aún hay saldo pendiente'];
        }

        if ($apartado['estado'] !== 'activo') {
            return ['success' => false, 'message' => 'El apartado no está activo'];
        }

        try {
            $this->conn->beginTransaction();

            // Crear venta
            $ventaModel = new Venta();
            $datos_venta = [
                'cliente_id' => $apartado['cliente_id'],
                'vendedor_id' => $usuario_id,
                'tipo_venta' => 'contado',
                'subtotal' => $apartado['monto_total'],
                'descuento' => 0,
                'total' => $apartado['monto_total'],
                'forma_pago' => 'abono_apartado',
                'observaciones' => 'Venta generada desde apartado ' . $apartado['numero_apartado'],
                'estado' => 'completada'
            ];
            $productos = [
                [
                    'iphone_id' => $apartado['iphone_id'],
                    'precio_unitario' => $apartado['monto_total'],
                    'subtotal' => $apartado['monto_total']
                ]
            ];

            $resultado = $ventaModel->crear($datos_venta, $productos);
            if (!$resultado['success']) {
                throw new Exception($resultado['message'] ?? 'Error al crear venta');
            }

            // Actualizar apartado a completado y asociar venta
            $query = "UPDATE {$this->table}
                      SET estado = 'completado', venta_id = :venta_id
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':venta_id', $resultado['venta_id']);
            $stmt->bindParam(':id', $apartado_id);
            $stmt->execute();

            $this->conn->commit();
            return ['success' => true, 'venta_id' => $resultado['venta_id']];
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error al completar apartado: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function cancelar($apartado_id) {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE {$this->table} SET estado = 'cancelado' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $apartado_id);
            $stmt->execute();

            // Liberar iPhone
            $query_iphone = "UPDATE iphones i
                             INNER JOIN {$this->table} a ON a.iphone_id = i.id
                             SET i.estado = 'disponible'
                             WHERE a.id = :id";
            $stmt_iphone = $this->conn->prepare($query_iphone);
            $stmt_iphone->bindParam(':id', $apartado_id);
            $stmt_iphone->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error al cancelar apartado: ' . $e->getMessage());
            return false;
        }
    }

    private function generarNumeroApartado() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();

        $numero = ($result['total'] + 1);
        $año = date('Y');

        return "APT-" . $año . "-" . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener estadísticas de apartados
     */
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
                    SUM(CASE WHEN estado = 'vencido' THEN 1 ELSE 0 END) as vencidos,
                    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(CASE WHEN estado = 'activo' THEN saldo_pendiente ELSE 0 END) as saldo_pendiente
                  FROM {$this->table}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
}
