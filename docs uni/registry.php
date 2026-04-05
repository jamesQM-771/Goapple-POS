<?php

class Registry {
    private static $servicios = [];

    /**
     * Método BIND (Vincular/Registrar con Validación)
     */
    public static function bind($nombreServicio, $direccionRed) {
        
        // Validación de disponibilidad y pre-existencia
        if (isset(self::$servicios[$nombreServicio])) {
            // Documentamos en log que el servicio se está actualizando
            error_log("Aviso: El servicio '{$nombreServicio}' ya existía. Actualizando la IP/Puerto para garantizar la interoperabilidad.");
        } else {
            error_log("Aviso: Registrando nuevo servicio '{$nombreServicio}' en el Registry.");
        }

        // Siempre guardamos (o sobrescribimos) el servicio para tener la referencia más actual.
        self::$servicios[$nombreServicio] = $direccionRed;
    }

    public static function lookup($nombreServicio) {
        if (isset(self::$servicios[$nombreServicio])) {
            return self::$servicios[$nombreServicio];
        }
        throw new Exception("El servicio '{$nombreServicio}' no se encuentra en el Registry.");
    }
}
