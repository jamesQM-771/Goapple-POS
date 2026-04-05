<?php
declare(strict_types=1);

/**
 * Actividad 1: Servidor que maneja Remote Callbacks.
 * Almacena referencias de los clientes y las invoca cuando ocurre un evento.
 */
class ServidorNotificaciones {
    // Lista de clientes activos y sus referencias de retorno
    private array $clientesActivos = [];

    /**
     * El servidor expone este método para que el cliente se registre
     */
    public function registrarCliente(string $clienteId, string $callbackReference): void {
        $this->clientesActivos[$clienteId] = $callbackReference;
        echo "[SERVIDOR] Registrado nuevo cliente '{$clienteId}' con Callback a: {$callbackReference}\n";
    }

    /**
     * Simula un evento asíncrono de negocio que hace "Push" de notificaciones
     */
    public function eventoStockActualizado(string $producto, int $nuevaCantidad): void {
        echo "\n[SERVIDOR] === EVENTO DE NEGOCIO: STOCK ACTUALIZADO ==\n";
        echo "[SERVIDOR] Producto: {$producto} | Nueva cantidad: {$nuevaCantidad}\n";
        
        // El servidor invoca remotamente las referencias almacenadas
        foreach ($this->clientesActivos as $id => $callbackRef) {
            $this->invocarCallback($id, $callbackRef, "Stock de {$producto} cambio a {$nuevaCantidad}");
        }
    }

    private function invocarCallback(string $id, string $url, string $mensaje): void {
        echo "[SERVIDOR -> CLIENTE {$id}] Invocando asincronamente '{$url}'...\n";
        // Aquí en un entorno DCOM o RMI, esto sería instanciar el proxy de cliente.
        // En HTTP sería un file_get_contents o cURL hacia el puerto/webhook del cliente.
        echo "   => Payload enviado: {$mensaje}\n";
    }
}
