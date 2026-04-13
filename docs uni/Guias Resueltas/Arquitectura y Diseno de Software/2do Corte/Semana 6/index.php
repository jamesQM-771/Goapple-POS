<?php
echo "<h1>Caso Testigo: Inserción del Negocio</h1>";
require_once 'app/Core/Producto.php';
require_once 'app/Data/ProductoDAO.php';

$entidadCelular = new Core\Producto("GoApple Z-Fold", 2599.99);
$gestorIntermedio = new Data\ProductoDAO();
$resultadoTransaccion = $gestorIntermedio->registrar($entidadCelular);

if($resultadoTransaccion){
    echo "[Motor Storage Ok] Datos inyectados.";
}
?>