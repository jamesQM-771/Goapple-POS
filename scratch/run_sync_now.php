<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/IntegrationController.php';

echo "--- Iniciando Sincronización Forzada ---\n";

$controller = new IntegrationController();
$result = $controller->syncAction();

if ($result['success']) {
    echo "Sincronización EXITOSA!\n";
    echo "Insertados: " . $result['stats']['insertados'] . "\n";
    echo "Actualizados: " . $result['stats']['actualizados'] . "\n";
} else {
    echo "ERROR en sincronización: " . $result['error'] . "\n";
}

$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT COUNT(*) as total FROM iphones");
$total = $stmt->fetch()['total'];
echo "Total de registros en tabla 'iphones': $total\n";
