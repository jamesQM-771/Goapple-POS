<?php
namespace App\Core;

class Logger {
    private static $logFile = __DIR__ . '/../../logs/audit.log';

    public static function log($tipoEvento, $mensajeDetallado) {
        $fechaHora = date('Y-m-d H:i:s');
        $logMessage = "[$fechaHora] [$tipoEvento] [$mensajeDetallado]" . PHP_EOL;
        
        // Crear directorio de logs si no existe
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
}
