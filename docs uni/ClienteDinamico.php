<?php
declare(strict_types=1);

require_once __DIR__ . '/registry.php';

final class ClienteCajaPOS
{
    private string $nombreServicio;

    public function __construct(string $nombreServicio)
    {
        $this->nombreServicio = $nombreServicio;
    }

    /**
     * Actividad 3 (Guia 6): lookup por nombre logico, sin IP hardcodeada.
     */
    public function procesarCobro(float $monto): void
    {
        echo "[CLIENTE] Solicitud de cobro: {$monto}" . PHP_EOL;

        try {
            $conexion = Registry::lookup($this->nombreServicio);
            $this->conectar($conexion['ip'], (int) $conexion['puerto'], $monto);
        } catch (Throwable $e) {
            echo "[CLIENTE] Error en lookup: {$e->getMessage()}" . PHP_EOL;
        }
    }

    private function conectar(string $ip, int $puerto, float $monto): void
    {
        echo "[CLIENTE] Conexion transparente a {$ip}:{$puerto}" . PHP_EOL;
        echo "[CLIENTE] Payload de cobro enviado por {$monto}" . PHP_EOL;
    }
}

// ---------------------------
// Demo para evidencia (Guia 6)
// ---------------------------
Registry::bind('ServicioPagos', ['ip' => '10.0.0.180', 'puerto' => 8080]);

$cliente = new ClienteCajaPOS('ServicioPagos');
$cliente->procesarCobro(1500.00);
