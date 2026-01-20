<?php
/**
 * API RESTful para operaciones AJAX
 * Maneja todas las peticiones del frontend
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

// Verificar autenticación
if (!estaLogueado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$action = $_GET['action'] ?? '';
$module = $_GET['module'] ?? '';

try {
    switch ($module) {
        case 'clientes':
            require_once __DIR__ . '/../models/Cliente.php';
            $model = new Cliente();
            handleCRUD($model, $action);
            break;

        case 'proveedores':
            require_once __DIR__ . '/../models/Proveedor.php';
            $model = new Proveedor();
            handleCRUD($model, $action);
            break;

        case 'iphones':
            require_once __DIR__ . '/../models/iPhone.php';
            $model = new iPhone();
            handleCRUD($model, $action);
            break;

        case 'ventas':
            require_once __DIR__ . '/../models/Venta.php';
            $model = new Venta();
            handleVentas($model, $action);
            break;

        case 'creditos':
            require_once __DIR__ . '/../models/Credito.php';
            $model = new Credito();
            handleCreditos($model, $action);
            break;

        case 'usuarios':
            if (!esAdmin()) {
                throw new Exception('Acceso denegado');
            }
            require_once __DIR__ . '/../models/Usuario.php';
            $model = new Usuario();
            handleCRUD($model, $action);
            break;

        default:
            throw new Exception('Módulo no válido');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Manejar operaciones CRUD genéricas
 */
function handleCRUD($model, $action) {
    switch ($action) {
        case 'list':
            $filtros = $_GET;
            unset($filtros['action'], $filtros['module']);
            $data = $model->obtenerTodos($filtros);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'get':
            $id = $_GET['id'] ?? 0;
            $data = $model->obtenerPorId($id);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
            }
            break;

        case 'create':
            $datos = json_decode(file_get_contents('php://input'), true);
            $id = $model->crear($datos);
            if ($id) {
                echo json_encode(['success' => true, 'id' => $id, 'message' => 'Creado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear']);
            }
            break;

        case 'update':
            $datos = json_decode(file_get_contents('php://input'), true);
            $id = $datos['id'] ?? 0;
            unset($datos['id']);
            $result = $model->actualizar($id, $datos);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Actualizado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
            }
            break;

        case 'delete':
            $id = $_GET['id'] ?? 0;
            $forzar = esAdmin() ? true : false;
            $result = $model->eliminar($id, $forzar);
            echo json_encode($result);
            break;

        default:
            throw new Exception('Acción no válida');
    }
}

/**
 * Manejar operaciones específicas de ventas
 */
function handleVentas($model, $action) {
    switch ($action) {
        case 'crear':
            $datos = json_decode(file_get_contents('php://input'), true);
            $productos = $datos['productos'] ?? [];
            unset($datos['productos']);
            $result = $model->crear($datos, $productos);
            echo json_encode($result);
            break;

        case 'detalle':
            $id = $_GET['id'] ?? 0;
            $detalle = $model->obtenerDetalle($id);
            echo json_encode(['success' => true, 'data' => $detalle]);
            break;

        case 'cancelar':
            $id = $_GET['id'] ?? 0;
            $motivo = $_GET['motivo'] ?? '';
            $result = $model->cancelar($id, $motivo);
            echo json_encode($result);
            break;

        default:
            handleCRUD($model, $action);
    }
}

/**
 * Manejar operaciones específicas de créditos
 */
function handleCreditos($model, $action) {
    switch ($action) {
        case 'calcular':
            $monto_total = $_GET['monto_total'] ?? 0;
            $cuota_inicial = $_GET['cuota_inicial'] ?? 0;
            $tasa_interes = $_GET['tasa_interes'] ?? TASA_INTERES_DEFAULT;
            $numero_cuotas = $_GET['numero_cuotas'] ?? 12;
            
            $calculo = $model->calcularCredito($monto_total, $cuota_inicial, $tasa_interes, $numero_cuotas);
            echo json_encode(['success' => true, 'data' => $calculo]);
            break;

        case 'pagar':
            $datos = json_decode(file_get_contents('php://input'), true);
            $result = $model->registrarPago($datos);
            echo json_encode($result);
            break;

        case 'pagos':
            $credito_id = $_GET['credito_id'] ?? 0;
            $pagos = $model->obtenerPagos($credito_id);
            echo json_encode(['success' => true, 'data' => $pagos]);
            break;

        case 'mora':
            $creditos = $model->obtenerEnMora();
            echo json_encode(['success' => true, 'data' => $creditos]);
            break;

        default:
            handleCRUD($model, $action);
    }
}
