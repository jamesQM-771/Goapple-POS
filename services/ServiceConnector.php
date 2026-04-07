<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: James y Giorgi Julian Ordoñez | Guía: 7 */
/**
 * ServiceConnector
 * Componente para interactuar con Web Services externos (REST)
 * Guía Práctica N° 7 - Arquitectura y Diseño de Software
 */

class ServiceConnector {
    private $apiUrl;
    private $timeout;

    public function __construct($apiUrl = 'https://dummyjson.com/products/category/smartphones', $timeout = 10) {
        $this->apiUrl = $apiUrl;
        $this->timeout = $timeout;
    }

    /**
     * Consume el servicio externo mediante GET
     * @return array Resumen de la operación y datos mapeados
     */
    public function fetchExternalProducts() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: GoApple-POS-Integration/1.0'
        ]);

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => "Error de conexión (Timeout o Red): " . $error,
                'status_code' => $httpCode
            ];
        }

        if ($httpCode >= 400) {
            return [
                'success' => false,
                'error' => "El servicio externo respondió con un error ($httpCode)",
                'status_code' => $httpCode,
                'body' => $responseBody
            ];
        }

        $data = json_decode($responseBody, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => "Error al decodificar JSON: " . json_last_error_msg(),
                'status_code' => $httpCode
            ];
        }

        // Mapeo de datos para el objeto iPhone
        $mappedProducts = $this->mapToBusinessObjects($data['products'] ?? []);

        return [
            'success' => true,
            'status_code' => $httpCode,
            'data' => $mappedProducts,
            'raw_count' => count($data['products'] ?? [])
        ];
    }

    /**
     * Mapea el JSON crudo del proveedor externo a la estructura de iPhone
     * @param array $externalProducts
     * @return array
     */
    private function mapToBusinessObjects($externalProducts) {
        $mapped = [];
        foreach ($externalProducts as $prod) {
            // Generamos un IMEI aleatorio para simular equipos reales
            $imei = '35' . str_pad(rand(0, 9999999999999), 13, '0', STR_PAD_LEFT);
            
            $mapped[] = [
                'modelo' => $prod['title'],
                'capacidad' => $this->extractCapacity($prod['title']) ?? '128GB',
                'color' => 'Varios',
                'condicion' => 'nuevo',
                'estado_bateria' => 100,
                'imei' => $imei,
                'precio_compra' => $prod['price'] * 4000, // Simulación de TRM a Pesos
                'precio_venta' => ($prod['price'] * 4000) * 1.3, // 30% utilidad
                'estado' => 'disponible',
                'observaciones' => "Importado via Web Service API: " . ($prod['description'] ?? '')
            ];
        }
        return $mapped;
    }

    private function extractCapacity($title) {
        if (preg_match('/(\d+GB|\d+TB)/i', $title, $matches)) {
            return strtoupper($matches[1]);
        }
        return null;
    }
}
