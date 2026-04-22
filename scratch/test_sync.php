<?php
require_once __DIR__ . '/../../services/ServiceConnector.php';

$connector = new ServiceConnector();
$results = $connector->fetchExternalProducts();

if ($results['success']) {
    echo "Success! Found " . count($results['data']) . " products.\n";
    echo "Sample product: " . $results['data'][0]['modelo'] . " - " . $results['data'][0]['precio_venta'] . " COP\n";
} else {
    echo "Failed: " . $results['error'] . "\n";
}
