<?php
declare(strict_types=1);

/**
 * Actividad 3: Optimización del Paso de Parámetros.
 * Evalúa tráfico por Valor vs por Referencia.
 */

class OptimizadorParametros {
    
    /**
     * Paso de Objeto por Valor:
     * El payload transmite la jerarquía completa del estado del modelo, es decir, 
     * el cliente serializa el objeto masivo y el servidor lo deserializa.
     */
    public function evaluarPasoPorValor(array $estadoMasivo): void {
        $payload = serialize($estadoMasivo);
        $tamanioBytes = strlen($payload);

        echo "--- PASO POR VALOR ---\n";
        echo "ESTRATEGIA: Los datos crudos cruzan la red.\n";
        echo "PAYLOAD SIZE: " . number_format($tamanioBytes, 0, ',', '.') . " Bytes transmitidos.\n";
        echo "ESTIMACIÓN: Ideal para objetos pequeños sin metadatos anidados. Alta latencia en payloads grandes.\n\n";
    }

    /**
     * Paso de Objeto por Referencia:
     * El payload transmite únicamente un puntero local (en DCOM un UUID) 
     * y el servidor llama de vuelta al cliente cuando necesita acceder al estado.
     */
    public function evaluarPasoPorReferencia(string $uuid_objeto_remoto): void {
        $payload = $uuid_objeto_remoto;
        $tamanioBytes = strlen($payload);

        echo "--- PASO POR REFERENCIA ---\n";
        echo "ESTRATEGIA: Solo transmite un UUID (Puntero logico).\n";
        echo "PAYLOAD SIZE: " . number_format($tamanioBytes, 0, ',', '.') . " Bytes transmitidos.\n";
        echo "ESTIMACIÓN: Minimiza ancho de banda inicial, ideal para objetos MVP ultra pesados o de uso demorado.\n\n";
    }
}

// ==========================================
// EJECUCIÓN COMPARATIVA
// ==========================================
$evaluador = new OptimizadorParametros();

// 1. Simulación de un Objeto Pesado (Paso por Valor)
// Supongamos un Carrito de compra o Historia clinica con 10,000 registros (simulados aquí)
$objetoMasivo = array_fill(0, 5000, ['id' => rand(1, 999), 'descripcion' => 'Item cargado de datos para rellenar payload de red con mucha info']);

// Ejecución
echo "INICIANDO BENCHMARK DE PARÁMETROS...\n\n";

$inicio_valor = microtime(true);
$evaluador->evaluarPasoPorValor($objetoMasivo);
$latencia_valor = microtime(true) - $inicio_valor;

// 2. Simulación de un Puntero DCOM/UUID (Paso por Referencia)
$UUID_referencia = 'E5B0E6C4-13E2-4D39-9B1D-66EB8CA3D811'; // Aprox 36 caracteres
$inicio_ref = microtime(true);
$evaluador->evaluarPasoPorReferencia($UUID_referencia);
$latencia_ref = microtime(true) - $inicio_ref;

// 3. Resultado Final Comparativo
echo "===== RESULTADOS DEL BENCHMARK =====\n";
echo "Latencia computacional local (Serialización):\n";
echo "Valor: " . round($latencia_valor * 1000, 2) . " ms | Referencia: " . round($latencia_ref * 1000, 2) . " ms\n";
echo "Conclusión MVP: Si se requiere baja latencia y menor de red para objetos monstruosos, pasar UUID/Reference es óptimo. Si son modelos base de poco peso, el Paso por Valor es más rápido de implementar sin callbacks.";
echo "\n====================================\n";
