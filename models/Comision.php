<?php
/**
 * Modelo Comision
 * Gestión de comisiones de vendedores
 */

require_once __DIR__ . '/../config/database.php';

class Comision {
    private $conn;
    private $tableConfig = 'comision_config';
    private $tablePagos = 'comision_pagos';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Obtener configuración de comisión por vendedor
     */
    public function obtenerConfig($vendedor_id) {
        $query = "SELECT * FROM {$this->tableConfig} WHERE vendedor_id = :vendedor_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vendedor_id', $vendedor_id);
        $stmt->execute();
        $config = $stmt->fetch();

        if ($config) {
            return $config;
        }

        return [
            'vendedor_id' => $vendedor_id,
            'porcentaje' => COMISION_DEFAULT_PCT,
            'meta_mensual' => 0,
            'bono_meta' => 0,
            'descuento_fijo' => 0,
            'retencion_pct' => 0
        ];
    }

    /**
     * Guardar configuración de comisión
     */
    public function guardarConfig($vendedor_id, $datos) {
        $query = "INSERT INTO {$this->tableConfig} 
                    (vendedor_id, porcentaje, meta_mensual, bono_meta, descuento_fijo, retencion_pct)
                  VALUES (:vendedor_id, :porcentaje, :meta_mensual, :bono_meta, :descuento_fijo, :retencion_pct)
                  ON DUPLICATE KEY UPDATE
                    porcentaje = VALUES(porcentaje),
                    meta_mensual = VALUES(meta_mensual),
                    bono_meta = VALUES(bono_meta),
                    descuento_fijo = VALUES(descuento_fijo),
                    retencion_pct = VALUES(retencion_pct)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vendedor_id', $vendedor_id);
        $stmt->bindParam(':porcentaje', $datos['porcentaje']);
        $stmt->bindParam(':meta_mensual', $datos['meta_mensual']);
        $stmt->bindParam(':bono_meta', $datos['bono_meta']);
        $stmt->bindParam(':descuento_fijo', $datos['descuento_fijo']);
        $stmt->bindParam(':retencion_pct', $datos['retencion_pct']);

        return $stmt->execute();
    }

    /**
     * Calcular comisiones mensuales
     */
    public function calcularMensual($mes, $anio, $vendedor_id = null) {
        $vendedores = [];
        if ($vendedor_id) {
            $vendedores[] = $vendedor_id;
        } else {
            $query = "SELECT id FROM usuarios WHERE rol = 'vendedor' AND estado = 'activo'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $vendedores = array_column($stmt->fetchAll(), 'id');
        }

        $procesados = 0;

        foreach ($vendedores as $vid) {
            $config = $this->obtenerConfig($vid);

            $queryVentas = "SELECT COUNT(*) as total_ventas, COALESCE(SUM(total),0) as total_vendido
                            FROM ventas
                            WHERE vendedor_id = :vendedor_id
                              AND estado = 'completada'
                              AND MONTH(fecha_venta) = :mes
                              AND YEAR(fecha_venta) = :anio";
            $stmtVentas = $this->conn->prepare($queryVentas);
            $stmtVentas->bindParam(':vendedor_id', $vid);
            $stmtVentas->bindParam(':mes', $mes);
            $stmtVentas->bindParam(':anio', $anio);
            $stmtVentas->execute();
            $ventas = $stmtVentas->fetch();

            $total_vendido = floatval($ventas['total_vendido'] ?? 0);
            $total_ventas = intval($ventas['total_ventas'] ?? 0);

            if ($total_ventas === 0) {
                continue;
            }

            $porcentaje = floatval($config['porcentaje'] ?? COMISION_DEFAULT_PCT);
            $comision_base = $total_vendido * ($porcentaje / 100);
            $bono = 0;
            if (floatval($config['meta_mensual']) > 0 && $total_vendido >= floatval($config['meta_mensual'])) {
                $bono = floatval($config['bono_meta']);
            }
            $descuento = floatval($config['descuento_fijo']);
            $retencion = max(0, ($comision_base + $bono - $descuento)) * (floatval($config['retencion_pct']) / 100);
            $total_pagar = max(0, $comision_base + $bono - $descuento - $retencion);

            $queryExiste = "SELECT id, estado FROM {$this->tablePagos} WHERE vendedor_id = :vendedor_id AND mes = :mes AND anio = :anio LIMIT 1";
            $stmtExiste = $this->conn->prepare($queryExiste);
            $stmtExiste->bindParam(':vendedor_id', $vid);
            $stmtExiste->bindParam(':mes', $mes);
            $stmtExiste->bindParam(':anio', $anio);
            $stmtExiste->execute();
            $existe = $stmtExiste->fetch();

            if ($existe && $existe['estado'] === 'pagada') {
                continue;
            }

            $query = "INSERT INTO {$this->tablePagos}
                        (vendedor_id, mes, anio, total_ventas, total_vendido, porcentaje, comision_base, bono, descuento, retencion, total_pagar)
                      VALUES (:vendedor_id, :mes, :anio, :total_ventas, :total_vendido, :porcentaje, :comision_base, :bono, :descuento, :retencion, :total_pagar)
                      ON DUPLICATE KEY UPDATE
                        total_ventas = VALUES(total_ventas),
                        total_vendido = VALUES(total_vendido),
                        porcentaje = VALUES(porcentaje),
                        comision_base = VALUES(comision_base),
                        bono = VALUES(bono),
                        descuento = VALUES(descuento),
                        retencion = VALUES(retencion),
                        total_pagar = VALUES(total_pagar),
                        fecha_calculo = CURRENT_TIMESTAMP";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':vendedor_id', $vid);
            $stmt->bindParam(':mes', $mes);
            $stmt->bindParam(':anio', $anio);
            $stmt->bindParam(':total_ventas', $total_ventas);
            $stmt->bindParam(':total_vendido', $total_vendido);
            $stmt->bindParam(':porcentaje', $porcentaje);
            $stmt->bindParam(':comision_base', $comision_base);
            $stmt->bindParam(':bono', $bono);
            $stmt->bindParam(':descuento', $descuento);
            $stmt->bindParam(':retencion', $retencion);
            $stmt->bindParam(':total_pagar', $total_pagar);
            $stmt->execute();
            $procesados++;
        }

        return $procesados;
    }

    /**
     * Obtener resumen de comisiones por mes
     */
    public function obtenerResumen($mes, $anio) {
        $query = "SELECT cp.*, u.nombre as vendedor_nombre
                  FROM {$this->tablePagos} cp
                  INNER JOIN usuarios u ON cp.vendedor_id = u.id
                  WHERE cp.mes = :mes AND cp.anio = :anio
                  ORDER BY cp.total_pagar DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mes', $mes);
        $stmt->bindParam(':anio', $anio);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener comisiones por vendedor
     */
    public function obtenerPorVendedor($vendedor_id) {
        $query = "SELECT * FROM {$this->tablePagos} WHERE vendedor_id = :vendedor_id ORDER BY anio DESC, mes DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vendedor_id', $vendedor_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Marcar comisión como pagada
     */
    public function marcarPagada($id, $usuario_id) {
        $query = "UPDATE {$this->tablePagos} SET estado = 'pagada', fecha_pago = NOW(), pagado_por = :pagado_por WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':pagado_por', $usuario_id);
        return $stmt->execute();
    }

    /**
     * Obtener ventas del mes por vendedor
     */
    public function obtenerVentasMes($vendedor_id, $mes, $anio) {
        $query = "SELECT v.*, c.nombre as cliente_nombre
                  FROM ventas v
                  INNER JOIN clientes c ON v.cliente_id = c.id
                  WHERE v.vendedor_id = :vendedor_id
                    AND v.estado = 'completada'
                    AND MONTH(v.fecha_venta) = :mes
                    AND YEAR(v.fecha_venta) = :anio
                  ORDER BY v.fecha_venta DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vendedor_id', $vendedor_id);
        $stmt->bindParam(':mes', $mes);
        $stmt->bindParam(':anio', $anio);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
