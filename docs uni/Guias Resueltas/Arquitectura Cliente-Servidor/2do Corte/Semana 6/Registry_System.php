<?php
/* Asignatura: Arquitectura Cliente-Servidor | Comentador: james | Guía: 6 | Punto de la guía: Actividad 1, 2 y 3 */

/**
 * Actividad 1: Configuración del Servicio de Directorio (Registry)
 * El registry actúa como tabla de búsqueda o directorio telefónico de servicios.
 */
class RegistryDirectory {
    private $servicios = [];

    /**
     * Actividad 2: Protocolo de Registro de Servicios (Bind)
     */
    public function bind($nombreLogico, $direccionIP, $puerto) {
        $this->servicios[$nombreLogico] = [
            'ip' => $direccionIP,
            'puerto' => $puerto
        ];
        echo "Servicio [$nombreLogico] registrado en {$direccionIP}:{$puerto}\n";
    }

    /**
     * Actividad 3: Búsqueda Dinámica desde el Cliente (Lookup)
     * Elimina las IPs "quemadas" en el código del cliente.
     */
    public function lookup($nombreLogico) {
        if (isset($this->servicios[$nombreLogico])) {
            return $this->servicios[$nombreLogico];
        }
        throw new Exception("Servicio no encontrado.");
    }
}

// Simulación:
$registry = new RegistryDirectory();
// Servidor se anuncia automáticamente
$registry->bind("AuthService", "192.168.1.100", 8081);

// Cliente busca dinámicamente y se conecta
$refServicio = $registry->lookup("AuthService");
echo "Cliente descubrió el servicio dinámicamente: Conectando a " . $refServicio['ip'] . ":" . $refServicio['puerto'] . "\n";
