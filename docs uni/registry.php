<?php
declare(strict_types=1);

/**
 * Registry simple en memoria para demostracion de naming system.
 * Permite bind (registrar/actualizar) y lookup (buscar).
 */
final class Registry
{
    /** @var array<string, array{ip:string, puerto:int, status?:string, actualizado_en:string}> */
    private static array $services = [];

    /**
     * Registra o actualiza la referencia de un servicio.
     *
     * @param string $serviceName Nombre logico del servicio.
     * @param array{ip:string, puerto:int, status?:string} $reference
     * @return array{resultado:string, mensaje:string}
     */
    public static function bind(string $serviceName, array $reference): array
    {
        if ($serviceName === '') {
            throw new InvalidArgumentException('El nombre del servicio no puede ser vacio.');
        }
        if (empty($reference['ip']) || empty($reference['puerto'])) {
            throw new InvalidArgumentException('La referencia debe incluir ip y puerto.');
        }

        $exists = array_key_exists($serviceName, self::$services);
        self::$services[$serviceName] = [
            'ip' => (string) $reference['ip'],
            'puerto' => (int) $reference['puerto'],
            'status' => $reference['status'] ?? 'ACTIVO',
            'actualizado_en' => date('Y-m-d H:i:s'),
        ];

        return [
            'resultado' => $exists ? 'updated' : 'created',
            'mensaje' => $exists
                ? "Servicio '{$serviceName}' actualizado en Registry."
                : "Servicio '{$serviceName}' registrado en Registry.",
        ];
    }

    /**
     * Busca la referencia de un servicio por nombre logico.
     *
     * @return array{ip:string, puerto:int, status?:string, actualizado_en:string}
     */
    public static function lookup(string $serviceName): array
    {
        if (!array_key_exists($serviceName, self::$services)) {
            throw new RuntimeException("Servicio '{$serviceName}' no encontrado en Registry.");
        }

        return self::$services[$serviceName];
    }

    /**
     * Solo para reporte/evidencia.
     *
     * @return array<string, array{ip:string, puerto:int, status?:string, actualizado_en:string}>
     */
    public static function all(): array
    {
        return self::$services;
    }
}
