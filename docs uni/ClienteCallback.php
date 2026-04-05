<?php
declare(strict_types=1);

require_once __DIR__ . '/ServidorCallback.php';

/**
 * Actividad 1: Stub Cliente modificado para enviar una Referencia de Retorno.
 */
class ClienteStub {
    private string $id;
    private string $puertoLocal;

    public function __construct(string $id, string $puertoLocal) {
        $this->id = $id;
        $this->puertoLocal = $puertoLocal;
    }

    /**
     * Al conectarse al servidor, no solo hace "Pull", sino que 
     * inscribe su propia referencia para recibir llamadas asíncronas (Push).
     */
    public function conectarYSubscribir(ServidorNotificaciones $servidor): void {
        echo "[CLIENTE {$this->id}] Conectando al servidor principal...\n";
        
        // La referencia de retorno puede ser una IP/Puerto o un UUID DCOM
        $callbackRef = "http://localhost:{$this->puertoLocal}/mi-interfaz-callback";
        echo "[CLIENTE {$this->id}] Enviando referencia de retorno: {$callbackRef}\n";
        
        $servidor->registrarCliente($this->id, $callbackRef);
    }
}

// ==========================================
// PRUEBA DEL FLUJO (MVP)
// ==========================================
$servidor = new ServidorNotificaciones();

$cliente1 = new ClienteStub('Caja-Chacaito', '9001');
$cliente2 = new ClienteStub('Caja-Altamira', '9002');

// Conexión y envío de callback reference
$cliente1->conectarYSubscribir($servidor);
$cliente2->conectarYSubscribir($servidor);

// Simulación: Ocurre un evento en el servidor y este notifica asíncronamente a los clientes
$servidor->eventoStockActualizado('Zapatos Nike', 15);
