<?php
/**
 * Registrar Pago de Crédito
 * Sistema POS GOapple
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Credito.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $credito_id = $_POST['credito_id'];
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];
    $observaciones = $_POST['observaciones'] ?? '';
    
    $creditoModel = new Credito();
    $resultado = $creditoModel->registrarPago($credito_id, $monto, $metodo_pago, $observaciones);
    
    if ($resultado) {
        header("Location: " . BASE_URL . "views/creditos/detalle.php?id=" . $credito_id . "&success=1");
    } else {
        header("Location: " . BASE_URL . "views/creditos/detalle.php?id=" . $credito_id . "&error=1");
    }
    exit();
}

header("Location: " . BASE_URL . "views/creditos/");
exit();
