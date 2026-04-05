<?php
// Requerimos el Registry para poder anunciarnos
require_once 'registry.php';

class ServicioPagosRemoto {
    
    private $mi_ip;
    private $mi_puerto;

    public function __construct($ip, $puerto) {
        // Inicializamos los datos del servidor
        $this->mi_ip = $ip;
        $this->mi_puerto = $puerto;
        
        // ¡Secreto de la Actividad 2!
        // Al instanciarse, el objeto se ANUNCIA automáticamente al Registry
        $this->anunciarseEnRed();
    }

    /**
     * Lógica de Auto-Registro en el Directorio
     */
    private function anunciarseEnRed() {
        $datosDeConexion = [
            'ip' => $this->mi_ip,
            'puerto' => $this->mi_puerto,
            'status' => 'Conectado y Disponible'
        ];
        
        // Invocamos al Registry publicando nuestro nombre y nuestros datos de acceso
        Registry::bind('ServicioPagos', $datosDeConexion);
    }
    
    // ... Resto de lógica de pagos ...
}

// ==========================================
// PRUEBA DEL FLUJO PARA TU REPORTE
// ==========================================

// Cuando el servidor arranque esta línea, el servicio "nace" y se registra automático.
$servidorPagos = new ServicioPagosRemoto('10.0.0.155', 8080);

// ¡Oh no! El servidor de pagos se reinició, cambió de IP y vuelve a iniciar:
$servidorPagosReiniciado = new ServicioPagosRemoto('10.0.0.180', 8080);
// Aquí el Registry detectará que ya existía e imprimirá el Aviso de actualización.

// Otro módulo busca a quién cobrar:
$referenciaActual = Registry::lookup('ServicioPagos');
echo "Nueva ruta del servicio de pagos: " . $referenciaActual['ip']; 
// Imprimirá 10.0.0.180 (garantizando interoperabilidad exitosa)
