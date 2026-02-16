<?php
// Requerimos el Registry para hacer las búsquedas lógicas
require_once 'registry.php';

class ClienteCajaPOS {
    
    // Aquí NO tenemos ninguna variable "$ip_fija = '192.168.1.50';" (Eliminada)
    private $nombreServicioDeseado;

    public function __construct($nombreServicio) {
        // En lugar de una IP, el cliente nace sabiendo solo el "Nombre Lógico"
        $this->nombreServicioDeseado = $nombreServicio;
    }

    /**
     * Esta función simula un intento de cobro a un cliente
     */
    public function procesarCobro($monto) {
        echo "Iniciando proceso de cobro por $monto...\n";

        try {
            // 1. Consulta al Registry enviando el "nombre lógico"
            // (Requisito: Búsqueda dinámica)
            $datosConexion = Registry::lookup($this->nombreServicioDeseado);
            
            // 2. Usar la respuesta para establecer la conexión transparente
            $this->conectarAlServicioRemoto($datosConexion['ip'], $datosConexion['puerto'], $monto);

        } catch (Exception $e) {
            echo "Error Crítico del Cliente: No pudimos encontrar el servicio. Detalle: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Función privada para simular la conexión final transparente
     */
    private function conectarAlServicioRemoto($ip, $puerto, $monto) {
        // Aquí iría tu cURL, Guzzle o Sockets reales.
        echo "ÉXITO: Conexión transparente establecida con éxito al nodo {$ip}:{$puerto}\n";
        echo "Enviando transacción de {$monto} al servidor remoto...\n";
    }
}

// ==========================================
// PRUEBA DEL FLUJO PARA TU REPORTE
// ==========================================

// Asegurémonos de que el servicio esté "vivo" en el registry (Lo que hicimos en Actividad 1 y 2)
Registry::bind('ServicioPagos', ['ip' => '10.0.0.99', 'puerto' => 443]);

// Creamos un Cliente 
// REQUISITO CUMPLIDO: Fíjate que al cliente NUNCA se le pasa un '10.0.0.99' quemado.
// Se le pasa puramente un Nombre Lógico (Naming System)
$cajaCliente = new ClienteCajaPOS('ServicioPagos');

// El cliente necesita operar. Buscará internamente en el Registry.
$cajaCliente->procesarCobro(1500.00);
