<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: James y Giorgi Julian Ordoñez | Guía: 7 */
/**
 * IntegrationController
 * Orquestador de la integración con Web Services
 * Guía Práctica N° 7
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/ServiceConnector.php';

class IntegrationController {
    
    /**
     * Sincroniza productos desde el servicio externo
     */
    public function syncAction() {
        // 1. Garantizar que existe el proveedor externo
        $proveedor_id = $this->getOrCreateExternalProvider();
        
        // 2. Consumir el Web Service
        $connector = new ServiceConnector();
        $response = $connector->fetchExternalProducts();
        
        if (!$response['success']) {
            return $response;
        }

        // 3. Preparar datos con el proveedor_id asignado
        $productosMapeados = $response['data'];
        foreach ($productosMapeados as &$prod) {
            $prod['proveedor_id'] = $proveedor_id;
        }

        // 4. Persistir en Base de Datos (Capa de Datos Refinada)
        $iphoneModel = new iPhone();
        $persistenceResult = $iphoneModel->upsertFromService($productosMapeados);

        if (!$persistenceResult['success']) {
            return [
                'success' => false,
                'error' => "Error en persistencia: " . $persistenceResult['error'],
                'status_code' => $response['status_code']
            ];
        }

        return [
            'success' => true,
            'message' => "Sincronización completada exitosamente",
            'stats' => $persistenceResult['stats'],
            'status_code' => $response['status_code'],
            'total_external' => $response['raw_count']
        ];
    }

    /**
     * Busca o crea un proveedor genérico para la API externa
     */
    private function getOrCreateExternalProvider() {
        $db = Database::getInstance()->getConnection();
        
        // Buscar por NIT fijo de la API
        $nit = '999999999-0';
        $stmt = $db->prepare("SELECT id FROM proveedores WHERE nit_cedula = ? LIMIT 1");
        $stmt->execute([$nit]);
        $proveedor = $stmt->fetch();

        if ($proveedor) {
            return $proveedor['id'];
        }

        // Crear si no existe
        $stmt = $db->prepare("INSERT INTO proveedores (nombre, empresa, nit_cedula, telefono, email, direccion, ciudad, estado) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'Proveedor API Externo', 
            'DummyJSON Global Supply', 
            $nit, 
            '+1 800 DUMMY', 
            'api@dummyjson.com', 
            'Silicon Valley', 
            'California', 
            'activo'
        ]);

        return $db->lastInsertId();
    }
}

// Lógica para peticiones AJAX
if (isset($_GET['action']) && $_GET['action'] === 'sync') {
    header('Content-Type: application/json');
    $controller = new IntegrationController();
    echo json_encode($controller->syncAction());
    exit();
}
