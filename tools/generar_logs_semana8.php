<?php
require_once __DIR__ . '/../app/Core/Logger.php';

use App\Core\Logger;

echo "Generando logs de auditoría...\n";

// 1. Intentar un login fallido
Logger::log('SEGURIDAD', 'Intento de login fallido - IP: 192.168.1.45 - Usuario: admin@goapple.com');
sleep(1);

// 2. Insertar un registro
Logger::log('OPERACION', 'Registro insertado exitosamente - Tabla: clientes - ID_Generado: 1042 - Acción por: admin(User_ID:1)');
sleep(1);

// 3. Consultar un dato
Logger::log('SISTEMA', 'Consulta de datos completada - Módulo: Inventario - Endpoint: /api/productos?id=77 - Resultado: 1 fila obtenida');

echo "Logs generados en /logs/audit.log\n";
