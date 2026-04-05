<?php
declare(strict_types=1);

require_once __DIR__ . '/registry.php';

final class ServicioPagosRemoto
{
    private string $ip;
    private int $puerto;

    public function __construct(string $ip, int $puerto)
    {
        $this->ip = $ip;
        $this->puerto = $puerto;
    }

    /**
     * Actividad 2 (Guia 6): auto-registro del servicio al iniciar.
     */
    public function iniciar(): void
    {
        $resultado = Registry::bind('ServicioPagos', [
            'ip' => $this->ip,
            'puerto' => $this->puerto,
            'status' => 'ACTIVO',
        ]);

        echo "[SERVIDOR] {$resultado['mensaje']}" . PHP_EOL;
    }
}

// ---------------------------
// Demo para evidencia (Guia 6)
// ---------------------------
$servicio = new ServicioPagosRemoto('10.0.0.155', 8080);
$servicio->iniciar();

// Simula reinicio/cambio de IP y actualizacion de referencia.
$servicioReiniciado = new ServicioPagosRemoto('10.0.0.180', 8080);
$servicioReiniciado->iniciar();

$referencia = Registry::lookup('ServicioPagos');
echo "[SERVIDOR] Ruta vigente de ServicioPagos: {$referencia['ip']}:{$referencia['puerto']}" . PHP_EOL;
