<?php
/**
 * Modelo Credito
 * Gestión de créditos con intereses
 */

require_once __DIR__ . '/../config/database.php';

class Credito {
    private $conn;
    private $table = 'creditos';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener todos los créditos
     */
    public function obtenerTodos($filtros = []) {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $buscar = '%' . $filtros['buscar'] . '%';
            $where[] = "(cr.numero_credito LIKE ? OR c.nombre LIKE ? OR c.cedula LIKE ?)";
            $params[] = $buscar;
            $params[] = $buscar;
            $params[] = $buscar;
        }

        if (!empty($filtros['estado'])) {
            $where[] = "cr.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['cliente_id'])) {
            $where[] = "cr.cliente_id = ?";
            $params[] = $filtros['cliente_id'];
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $query = "SELECT cr.*, 
                         c.nombre as cliente_nombre, c.cedula as cliente_cedula, c.telefono as cliente_telefono,
                         v.numero_venta
                  FROM " . $this->table . " cr
                  INNER JOIN clientes c ON cr.cliente_id = c.id
                  INNER JOIN ventas v ON cr.venta_id = v.id
                  $whereClause 
                  ORDER BY cr.fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener crédito por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT cr.*, 
                         c.nombre as cliente_nombre, c.cedula as cliente_cedula, 
                         c.telefono as cliente_telefono, c.direccion as cliente_direccion,
                         v.numero_venta, v.total as venta_total
                  FROM " . $this->table . " cr
                  INNER JOIN clientes c ON cr.cliente_id = c.id
                  INNER JOIN ventas v ON cr.venta_id = v.id
                  WHERE cr.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        return null;
    }

    /**
     * Crear nuevo crédito
     */
    public function crear($datos) {
        try {
            $this->conn->beginTransaction();

            // Calcular valores del crédito
            $calculo = $this->calcularCredito(
                $datos['monto_total'],
                $datos['cuota_inicial'],
                $datos['tasa_interes'],
                $datos['numero_cuotas']
            );

            // Generar número de crédito
            $numero_credito = $this->generarNumeroCredito();

            // Calcular fecha de primera cuota
            $fecha_inicio = $datos['fecha_inicio'] ?? date('Y-m-d');
            $fecha_primer_cuota = date('Y-m-d', strtotime($fecha_inicio . ' +1 month'));

            // Insertar crédito
            $query = "INSERT INTO " . $this->table . " 
                      (numero_credito, venta_id, cliente_id, monto_total, cuota_inicial, monto_financiado, 
                       tasa_interes, numero_cuotas, valor_cuota, total_intereses, total_pagado, saldo_pendiente, 
                       fecha_inicio, fecha_primer_cuota, estado) 
                      VALUES (:numero_credito, :venta_id, :cliente_id, :monto_total, :cuota_inicial, :monto_financiado, 
                              :tasa_interes, :numero_cuotas, :valor_cuota, :total_intereses, :total_pagado, :saldo_pendiente, 
                              :fecha_inicio, :fecha_primer_cuota, :estado)";

            $stmt = $this->conn->prepare($query);

            $total_pagado = $datos['cuota_inicial'];
            $saldo_pendiente = $calculo['monto_financiado'] + $calculo['total_intereses'];
            $estado = 'activo';

            $stmt->bindParam(':numero_credito', $numero_credito);
            $stmt->bindParam(':venta_id', $datos['venta_id']);
            $stmt->bindParam(':cliente_id', $datos['cliente_id']);
            $stmt->bindParam(':monto_total', $datos['monto_total']);
            $stmt->bindParam(':cuota_inicial', $datos['cuota_inicial']);
            $stmt->bindParam(':monto_financiado', $calculo['monto_financiado']);
            $stmt->bindParam(':tasa_interes', $datos['tasa_interes']);
            $stmt->bindParam(':numero_cuotas', $datos['numero_cuotas']);
            $stmt->bindParam(':valor_cuota', $calculo['valor_cuota']);
            $stmt->bindParam(':total_intereses', $calculo['total_intereses']);
            $stmt->bindParam(':total_pagado', $total_pagado);
            $stmt->bindParam(':saldo_pendiente', $saldo_pendiente);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_primer_cuota', $fecha_primer_cuota);
            $stmt->bindParam(':estado', $estado);

            $stmt->execute();
            $credito_id = $this->conn->lastInsertId();

            // Si hay cuota inicial, registrar el pago
            if ($datos['cuota_inicial'] > 0) {
                $this->registrarPago([
                    'credito_id' => $credito_id,
                    'monto_pago' => $datos['cuota_inicial'],
                    'numero_cuota' => 0,
                    'forma_pago' => $datos['forma_pago_inicial'] ?? 'efectivo',
                    'usuario_id' => $datos['usuario_id'],
                    'observaciones' => 'Cuota inicial'
                ], false); // false = no hacer commit aquí
            }

            $this->conn->commit();
            return ['success' => true, 'credito_id' => $credito_id, 'numero_credito' => $numero_credito];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error al crear crédito: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear el crédito: ' . $e->getMessage()];
        }
    }

    /**
     * Calcular valores del crédito con interés compuesto
     */
    public function calcularCredito($monto_total, $cuota_inicial, $tasa_interes, $numero_cuotas) {
        $monto_financiado = $monto_total - $cuota_inicial;
        $tasa_mensual = $tasa_interes / 100;

        // Fórmula de cuota fija con interés compuesto
        $valor_cuota = $monto_financiado * 
                       ($tasa_mensual * pow(1 + $tasa_mensual, $numero_cuotas)) / 
                       (pow(1 + $tasa_mensual, $numero_cuotas) - 1);

        $total_intereses = ($valor_cuota * $numero_cuotas) - $monto_financiado;

        return [
            'monto_financiado' => round($monto_financiado, 2),
            'valor_cuota' => round($valor_cuota, 2),
            'total_intereses' => round($total_intereses, 2),
            'total_a_pagar' => round($valor_cuota * $numero_cuotas, 2)
        ];
    }

    /**
     * Registrar pago de cuota
     */
    public function registrarPago($datos, $hacer_commit = true) {
        try {
            if ($hacer_commit) {
                $this->conn->beginTransaction();
            }

            // Generar número de recibo
            $numero_recibo = $this->generarNumeroRecibo();

            // Insertar pago
            $query = "INSERT INTO pagos_credito 
                      (numero_recibo, credito_id, monto_pago, numero_cuota, forma_pago, usuario_id, observaciones) 
                      VALUES (:numero_recibo, :credito_id, :monto_pago, :numero_cuota, :forma_pago, :usuario_id, :observaciones)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':numero_recibo', $numero_recibo);
            $stmt->bindParam(':credito_id', $datos['credito_id']);
            $stmt->bindParam(':monto_pago', $datos['monto_pago']);
            $stmt->bindParam(':numero_cuota', $datos['numero_cuota']);
            $stmt->bindParam(':forma_pago', $datos['forma_pago']);
            $stmt->bindParam(':usuario_id', $datos['usuario_id']);
            $stmt->bindParam(':observaciones', $datos['observaciones']);

            $stmt->execute();
            $pago_id = $this->conn->lastInsertId();

            // Actualizar crédito (el trigger lo hace, pero lo hacemos manualmente también)
            $this->actualizarSaldoCredito($datos['credito_id']);

            if ($hacer_commit) {
                $this->conn->commit();
            }

            return ['success' => true, 'pago_id' => $pago_id, 'numero_recibo' => $numero_recibo];

        } catch (Exception $e) {
            if ($hacer_commit) {
                $this->conn->rollBack();
            }
            error_log("Error al registrar pago: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar saldo del crédito
     */
    private function actualizarSaldoCredito($credito_id) {
        // Obtener total pagado
        $query = "SELECT COALESCE(SUM(monto_pago), 0) as total_pagado FROM pagos_credito WHERE credito_id = :credito_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':credito_id', $credito_id);
        $stmt->execute();
        $result = $stmt->fetch();
        $total_pagado = $result['total_pagado'];

        // Obtener datos del crédito
        $query = "SELECT monto_financiado, total_intereses FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $credito_id);
        $stmt->execute();
        $credito = $stmt->fetch();

        $total_deuda = $credito['monto_financiado'] + $credito['total_intereses'];
        $saldo_pendiente = $total_deuda - $total_pagado;
        $nuevo_estado = $saldo_pendiente <= 0 ? 'pagado' : 'activo';

        // Actualizar crédito
        $query = "UPDATE " . $this->table . " 
                  SET total_pagado = :total_pagado, saldo_pendiente = :saldo_pendiente, estado = :estado 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':total_pagado', $total_pagado);
        $stmt->bindParam(':saldo_pendiente', $saldo_pendiente);
        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':id', $credito_id);
        return $stmt->execute();
    }

    /**
     * Obtener pagos de un crédito
     */
    public function obtenerPagos($credito_id) {
        $query = "SELECT pc.*, u.nombre as usuario_nombre
                  FROM pagos_credito pc
                  LEFT JOIN usuarios u ON pc.usuario_id = u.id
                  WHERE pc.credito_id = :credito_id
                  ORDER BY pc.fecha_pago DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':credito_id', $credito_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Generar número consecutivo de crédito
     */
    private function generarNumeroCredito() {
        $query = "SELECT numero_credito FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $ultimo = $stmt->fetch();
            $numero = intval(substr($ultimo['numero_credito'], 2)) + 1;
        } else {
            $numero = 1;
        }

        return 'CR' . str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Generar número consecutivo de recibo
     */
    private function generarNumeroRecibo() {
        $query = "SELECT numero_recibo FROM pagos_credito ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $ultimo = $stmt->fetch();
            $numero = intval(substr($ultimo['numero_recibo'], 2)) + 1;
        } else {
            $numero = 1;
        }

        return 'RC' . str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Verificar y actualizar moras
     */
    public function verificarMoras() {
        $query = "CALL verificar_moras()";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    /**
     * Obtener créditos en mora
     */
    public function obtenerEnMora() {
        $query = "SELECT cr.*, 
                         c.nombre as cliente_nombre, c.cedula as cliente_cedula, c.telefono as cliente_telefono
                  FROM " . $this->table . " cr
                  INNER JOIN clientes c ON cr.cliente_id = c.id
                  WHERE cr.estado = 'mora'
                  ORDER BY cr.dias_mora DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener estadísticas de créditos
     */
    public function obtenerEstadisticas($fecha_desde = null, $fecha_hasta = null) {
        $where = [];
        $params = [];

        if ($fecha_desde) {
            $where[] = "DATE(fecha_creacion) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }

        if ($fecha_hasta) {
            $where[] = "DATE(fecha_creacion) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT 
                    COUNT(*) as total_creditos,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'pagado' THEN 1 ELSE 0 END) as pagados,
                    SUM(CASE WHEN estado = 'mora' THEN 1 ELSE 0 END) as en_mora,
                    SUM(monto_total) as monto_total_creditos,
                    SUM(monto_financiado) as total_financiado,
                    SUM(total_intereses) as total_intereses_generados,
                    SUM(total_pagado) as total_recaudado,
                    SUM(CASE WHEN estado IN ('activo', 'mora') THEN saldo_pendiente ELSE 0 END) as saldo_por_cobrar
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
     * Obtener próximos vencimientos
     */
    public function obtenerProximosVencimientos($dias = 7) {
        $fecha_limite = date('Y-m-d', strtotime("+$dias days"));

        $query = "SELECT cr.*, 
                         c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                         (SELECT COUNT(*) FROM pagos_credito WHERE credito_id = cr.id) as cuotas_pagadas
                  FROM " . $this->table . " cr
                  INNER JOIN clientes c ON cr.cliente_id = c.id
                  WHERE cr.estado = 'activo'
                  AND DATE_ADD(cr.fecha_primer_cuota, 
                      INTERVAL (SELECT COUNT(*) FROM pagos_credito WHERE credito_id = cr.id) MONTH
                  ) <= :fecha_limite
                  ORDER BY DATE_ADD(cr.fecha_primer_cuota, 
                      INTERVAL (SELECT COUNT(*) FROM pagos_credito WHERE credito_id = cr.id) MONTH
                  )";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha_limite', $fecha_limite);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
