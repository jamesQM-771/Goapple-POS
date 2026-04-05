<?php
declare(strict_types=1);

/**
 * Guia 7 - Demo completo:
 * 1) Remote callbacks
 * 2) Simulacion DCOM (UUID + registro)
 * 3) Optimizacion valor vs referencia
 */

interface CallbackEndpoint
{
    public function onNotify(string $evento, array $payload): void;
}

final class ClienteCallbackEndpoint implements CallbackEndpoint
{
    public function __construct(private readonly string $clientId)
    {
    }

    public function onNotify(string $evento, array $payload): void
    {
        echo "[CALLBACK][{$this->clientId}] {$evento} -> " . json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
}

final class ServidorEventos
{
    /** @var array<string, CallbackEndpoint> */
    private array $callbacks = [];

    /** @var array<string, array{service:string, uuid:string}> */
    private array $registroComponentes = [];

    public function registrarCliente(string $clientId, CallbackEndpoint $endpoint): void
    {
        $this->callbacks[$clientId] = $endpoint;
        echo "[SERVIDOR] Callback registrado para {$clientId}" . PHP_EOL;
    }

    public function registrarComponente(string $serviceName): string
    {
        $uuid = self::uuidV4();
        $this->registroComponentes[$serviceName] = [
            'service' => $serviceName,
            'uuid' => $uuid,
        ];
        echo "[DCOM-SIM] {$serviceName} registrado con UUID {$uuid}" . PHP_EOL;
        return $uuid;
    }

    public function emitirEvento(string $evento, array $payload): void
    {
        foreach ($this->callbacks as $clientId => $endpoint) {
            echo "[SERVIDOR] Notificando a {$clientId}" . PHP_EOL;
            $endpoint->onNotify($evento, $payload);
        }
    }

    private static function uuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

final class StubClienteRemoto
{
    public function __construct(
        private readonly string $clientId,
        private readonly CallbackEndpoint $endpoint
    ) {
    }

    // Actividad 1: el stub envia la callback reference al conectarse.
    public function conectarYRegistrarCallback(ServidorEventos $server): void
    {
        $server->registrarCliente($this->clientId, $this->endpoint);
    }
}

final class ParametroBenchmark
{
    /** @return array{bytes:int, ms:float} */
    public static function enviarPorValor(array $objeto): array
    {
        $inicio = microtime(true);
        $wire = json_encode($objeto, JSON_UNESCAPED_UNICODE);
        json_decode((string) $wire, true);
        $ms = (microtime(true) - $inicio) * 1000;
        return ['bytes' => strlen((string) $wire), 'ms' => $ms];
    }

    /** @return array{bytes:int, ms:float} */
    public static function enviarPorReferencia(string $objId, array $repositorio): array
    {
        $inicio = microtime(true);
        // En red solo viaja el id (puntero logico). El objeto se resuelve en destino.
        $wire = json_encode(['obj_id' => $objId]);
        $obj = $repositorio[$objId] ?? null;
        if ($obj === null) {
            throw new RuntimeException('Objeto no encontrado en repositorio remoto.');
        }
        $ms = (microtime(true) - $inicio) * 1000;
        return ['bytes' => strlen((string) $wire), 'ms' => $ms];
    }
}

function construirObjetoComplejo(int $items = 400): array
{
    $list = [];
    for ($i = 1; $i <= $items; $i++) {
        $list[] = [
            'sku' => 'IP-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
            'modelo' => 'iPhone ' . (14 + ($i % 3)),
            'precio' => 2500000 + ($i * 1000),
            'stock' => 1 + ($i % 25),
        ];
    }

    return [
        'tipo' => 'inventario_actualizado',
        'timestamp' => date('c'),
        'items' => $list,
    ];
}

// ---------------------------
// Ejecucion demostrativa
// ---------------------------
$server = new ServidorEventos();
$server->registrarComponente('ServicioNotificacionesPOS');

$clienteA = new StubClienteRemoto('cliente-caja-01', new ClienteCallbackEndpoint('cliente-caja-01'));
$clienteB = new StubClienteRemoto('cliente-caja-02', new ClienteCallbackEndpoint('cliente-caja-02'));

$clienteA->conectarYRegistrarCallback($server);
$clienteB->conectarYRegistrarCallback($server);

$server->emitirEvento('Stock actualizado', ['producto' => 'iPhone 14', 'nuevo_stock' => 3]);

$payload = construirObjetoComplejo();
$repo = ['obj-001' => $payload];

$porValor = ParametroBenchmark::enviarPorValor($payload);
$porReferencia = ParametroBenchmark::enviarPorReferencia('obj-001', $repo);

echo PHP_EOL . '--- Benchmark Valor vs Referencia ---' . PHP_EOL;
echo 'Por valor      -> bytes: ' . $porValor['bytes'] . ' | ms: ' . number_format($porValor['ms'], 4) . PHP_EOL;
echo 'Por referencia -> bytes: ' . $porReferencia['bytes'] . ' | ms: ' . number_format($porReferencia['ms'], 4) . PHP_EOL;

echo PHP_EOL . 'Conclusión: por referencia reduce bytes transmitidos cuando el objeto es grande.' . PHP_EOL;
