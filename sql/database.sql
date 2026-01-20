-- ========================================
-- SISTEMA POS GOAPPLE - BASE DE DATOS COMPLETA
-- Sistema de Ventas de iPhones con Créditos
-- ========================================

CREATE DATABASE IF NOT EXISTS goapple_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE goapple_pos;

-- ========================================
-- TABLA: usuarios
-- ========================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'vendedor') DEFAULT 'vendedor',
    telefono VARCHAR(20),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: proveedores
-- ========================================
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    empresa VARCHAR(150),
    nit_cedula VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    ciudad VARCHAR(100),
    pais VARCHAR(100) DEFAULT 'Colombia',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nit (nit_cedula),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: clientes
-- ========================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    direccion TEXT,
    ciudad VARCHAR(100),
    estado ENUM('activo', 'moroso', 'bloqueado') DEFAULT 'activo',
    limite_credito DECIMAL(15,2) DEFAULT 0.00,
    credito_disponible DECIMAL(15,2) DEFAULT 0.00,
    total_compras DECIMAL(15,2) DEFAULT 0.00,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notas TEXT,
    INDEX idx_cedula (cedula),
    INDEX idx_estado (estado),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: iphones (inventario)
-- ========================================
CREATE TABLE iphones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    capacidad VARCHAR(20) NOT NULL,
    color VARCHAR(50) NOT NULL,
    condicion ENUM('nuevo', 'usado') DEFAULT 'nuevo',
    estado_bateria INT DEFAULT 100 COMMENT 'Porcentaje de salud de batería',
    imei VARCHAR(20) NOT NULL UNIQUE,
    proveedor_id INT,
    precio_compra DECIMAL(15,2) NOT NULL,
    precio_venta DECIMAL(15,2) NOT NULL,
    estado ENUM('disponible', 'vendido', 'en_credito', 'apartado') DEFAULT 'disponible',
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_venta TIMESTAMP NULL,
    observaciones TEXT,
    INDEX idx_imei (imei),
    INDEX idx_estado (estado),
    INDEX idx_modelo (modelo),
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: ventas
-- ========================================
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_venta VARCHAR(20) NOT NULL UNIQUE,
    cliente_id INT NOT NULL,
    vendedor_id INT NOT NULL,
    tipo_venta ENUM('contado', 'credito') DEFAULT 'contado',
    subtotal DECIMAL(15,2) NOT NULL,
    descuento DECIMAL(15,2) DEFAULT 0.00,
    total DECIMAL(15,2) NOT NULL,
    forma_pago VARCHAR(50) DEFAULT 'efectivo',
    estado ENUM('completada', 'cancelada', 'pendiente') DEFAULT 'completada',
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    INDEX idx_numero_venta (numero_venta),
    INDEX idx_cliente (cliente_id),
    INDEX idx_vendedor (vendedor_id),
    INDEX idx_fecha (fecha_venta),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: detalle_ventas
-- ========================================
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    iphone_id INT NOT NULL,
    precio_unitario DECIMAL(15,2) NOT NULL,
    cantidad INT DEFAULT 1,
    subtotal DECIMAL(15,2) NOT NULL,
    INDEX idx_venta (venta_id),
    INDEX idx_iphone (iphone_id),
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (iphone_id) REFERENCES iphones(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: creditos
-- ========================================
CREATE TABLE creditos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_credito VARCHAR(20) NOT NULL UNIQUE,
    venta_id INT NOT NULL UNIQUE,
    cliente_id INT NOT NULL,
    monto_total DECIMAL(15,2) NOT NULL,
    cuota_inicial DECIMAL(15,2) DEFAULT 0.00,
    monto_financiado DECIMAL(15,2) NOT NULL,
    tasa_interes DECIMAL(5,2) NOT NULL COMMENT 'Tasa mensual en porcentaje',
    numero_cuotas INT NOT NULL,
    valor_cuota DECIMAL(15,2) NOT NULL,
    total_intereses DECIMAL(15,2) DEFAULT 0.00,
    total_pagado DECIMAL(15,2) DEFAULT 0.00,
    saldo_pendiente DECIMAL(15,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_primer_cuota DATE NOT NULL,
    estado ENUM('activo', 'pagado', 'mora', 'cancelado') DEFAULT 'activo',
    dias_mora INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_numero_credito (numero_credito),
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado),
    INDEX idx_venta (venta_id),
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: pagos_credito
-- ========================================
CREATE TABLE pagos_credito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_recibo VARCHAR(20) NOT NULL UNIQUE,
    credito_id INT NOT NULL,
    monto_pago DECIMAL(15,2) NOT NULL,
    numero_cuota INT,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    forma_pago VARCHAR(50) DEFAULT 'efectivo',
    usuario_id INT,
    observaciones TEXT,
    INDEX idx_credito (credito_id),
    INDEX idx_numero_recibo (numero_recibo),
    INDEX idx_fecha (fecha_pago),
    FOREIGN KEY (credito_id) REFERENCES creditos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: apartados
-- ========================================
CREATE TABLE apartados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_apartado VARCHAR(20) NOT NULL UNIQUE,
    cliente_id INT NOT NULL,
    iphone_id INT NOT NULL,
    valor_total DECIMAL(15,2) NOT NULL,
    abono_inicial DECIMAL(15,2) DEFAULT 0.00,
    saldo_pendiente DECIMAL(15,2) NOT NULL,
    dias_validez INT DEFAULT 7,
    fecha_apartado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_limite DATE NOT NULL,
    estado ENUM('activo', 'completado', 'cancelado', 'vencido') DEFAULT 'activo',
    observaciones TEXT,
    INDEX idx_numero (numero_apartado),
    INDEX idx_cliente (cliente_id),
    INDEX idx_iphone (iphone_id),
    INDEX idx_estado (estado),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (iphone_id) REFERENCES iphones(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: abonos_apartado
-- ========================================
CREATE TABLE abonos_apartado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apartado_id INT NOT NULL,
    monto DECIMAL(15,2) NOT NULL,
    fecha_abono TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    observaciones TEXT,
    INDEX idx_apartado (apartado_id),
    INDEX idx_fecha (fecha_abono),
    FOREIGN KEY (apartado_id) REFERENCES apartados(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: devoluciones
-- ========================================
CREATE TABLE devoluciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_devolucion VARCHAR(20) NOT NULL UNIQUE,
    venta_id INT NOT NULL,
    cliente_id INT NOT NULL,
    iphone_id INT NOT NULL,
    motivo TEXT NOT NULL,
    tipo ENUM('devolucion', 'cambio') DEFAULT 'devolucion',
    estado ENUM('solicitada', 'aprobada', 'rechazada', 'completada') DEFAULT 'solicitada',
    monto_devuelto DECIMAL(15,2) DEFAULT 0.00,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_procesamiento TIMESTAMP NULL,
    usuario_procesa_id INT,
    observaciones TEXT,
    INDEX idx_numero (numero_devolucion),
    INDEX idx_venta (venta_id),
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado),
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (iphone_id) REFERENCES iphones(id),
    FOREIGN KEY (usuario_procesa_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: comision_config
-- ========================================
CREATE TABLE comision_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_id INT NOT NULL UNIQUE,
    porcentaje DECIMAL(5,2) DEFAULT 2.00 COMMENT 'Porcentaje de comisión sobre ventas',
    meta_mensual DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Meta de ventas mensual',
    bono_meta DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Bono al cumplir meta',
    descuento_fijo DECIMAL(15,2) DEFAULT 0.00,
    retencion_pct DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de retención',
    fecha_inicio DATE,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    INDEX idx_vendedor (vendedor_id),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: comision_pagos
-- ========================================
CREATE TABLE comision_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_id INT NOT NULL,
    periodo DATE NOT NULL COMMENT 'Período del pago (fecha del mes)',
    total_ventas DECIMAL(15,2) DEFAULT 0.00,
    comision_base DECIMAL(15,2) DEFAULT 0.00,
    bono_cumplimiento DECIMAL(15,2) DEFAULT 0.00,
    retencion DECIMAL(15,2) DEFAULT 0.00,
    total_pagar DECIMAL(15,2) NOT NULL,
    estado ENUM('pendiente', 'pagado', 'cancelado') DEFAULT 'pendiente',
    fecha_pago TIMESTAMP NULL,
    usuario_paga_id INT,
    observaciones TEXT,
    INDEX idx_vendedor (vendedor_id),
    INDEX idx_periodo (periodo),
    INDEX idx_estado (estado),
    FOREIGN KEY (vendedor_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_paga_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: notificaciones
-- ========================================
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('info', 'warning', 'success', 'error') DEFAULT 'info',
    titulo VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    icono VARCHAR(50) DEFAULT '',
    enlace VARCHAR(255) DEFAULT '',
    leida ENUM('no', 'si') DEFAULT 'no',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario_id),
    INDEX idx_leida (leida),
    INDEX idx_fecha (fecha_creacion),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: fotos_compra
-- ========================================
CREATE TABLE fotos_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    iphone_id INT NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_carga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (iphone_id) REFERENCES iphones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_iphone (iphone_id),
    INDEX idx_fecha (fecha_carga)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: fotos_venta
-- ========================================
CREATE TABLE fotos_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    iphone_id INT,
    archivo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_carga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (iphone_id) REFERENCES iphones(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_venta (venta_id),
    INDEX idx_iphone (iphone_id),
    INDEX idx_fecha (fecha_carga)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABLA: configuracion
-- ========================================
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    descripcion TEXT,
    INDEX idx_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- ÍNDICES ADICIONALES
-- ========================================
CREATE INDEX idx_ventas_fecha_tipo ON ventas(fecha_venta, tipo_venta);
CREATE INDEX idx_creditos_estado_cliente ON creditos(estado, cliente_id);
CREATE INDEX idx_pagos_fecha ON pagos_credito(fecha_pago);
CREATE INDEX idx_iphones_modelo_estado ON iphones(modelo, estado);

-- ========================================
-- INSERTAR CONFIGURACIONES INICIALES
-- ========================================
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('empresa_nombre', 'GOAPPLE', 'Nombre de la empresa'),
('empresa_nit', '900123456-7', 'NIT de la empresa'),
('empresa_telefono', '+57 300 123 4567', 'Teléfono de contacto'),
('empresa_email', 'ventas@goapple.com', 'Email de contacto'),
('empresa_direccion', 'Calle 123 #45-67, Bogotá, Colombia', 'Dirección física'),
('tasa_interes_default', '3.5', 'Tasa de interés mensual por defecto (%)'),
('dias_mora_tolerancia', '5', 'Días de tolerancia antes de marcar mora'),
('penalizacion_mora', '5', 'Porcentaje de penalización por mora'),
('comision_default_pct', '2.0', 'Porcentaje de comisión por defecto para vendedores'),
('comision_meta_mensual', '10000000', 'Meta mensual para bono de comisión'),
('comision_bono_meta', '200000', 'Bono por cumplir meta de ventas');

-- ========================================
-- INSERTAR USUARIOS INICIALES
-- ========================================
INSERT INTO usuarios (nombre, email, password, rol, telefono, estado) VALUES
('Administrador', 'admin@goapple.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', '+57 300 123 4567', 'activo'),
('Vendedor Demo', 'vendedor@goapple.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor', '+57 310 987 6543', 'activo');

-- Password para ambos usuarios: admin123

-- ========================================
-- INSERTAR DATOS DE PRUEBA
-- ========================================

-- Proveedores
INSERT INTO proveedores (nombre, empresa, nit_cedula, telefono, email, direccion, ciudad) VALUES
('Juan Pérez', 'Importaciones JP', '900111222-3', '+57 301 111 2222', 'juan@importjp.com', 'Cra 7 #32-16', 'Bogotá'),
('María Rodríguez', 'TechSupply SAS', '900333444-5', '+57 302 333 4444', 'maria@techsupply.com', 'Calle 100 #15-20', 'Medellín'),
('Tech Import Colombia', 'Tech Import Colombia SAS', '900555666-7', '+57 303 555 6666', 'info@techimport.com', 'Av. El Dorado #68-90', 'Bogotá');

-- Clientes
INSERT INTO clientes (nombre, cedula, telefono, email, direccion, ciudad, limite_credito, credito_disponible) VALUES
('Carlos Gómez', '1012345678', '+57 310 111 2233', 'carlos@email.com', 'Calle 50 #20-30', 'Bogotá', 5000000.00, 5000000.00),
('Ana Martínez', '1023456789', '+57 311 222 3344', 'ana@email.com', 'Cra 15 #40-50', 'Cali', 3000000.00, 3000000.00),
('Luis Torres', '1034567890', '+57 312 333 4455', 'luis@email.com', 'Calle 80 #10-20', 'Medellín', 4000000.00, 4000000.00);

-- iPhones (Inventario)
INSERT INTO iphones (modelo, capacidad, color, condicion, estado_bateria, imei, proveedor_id, precio_compra, precio_venta, estado) VALUES
('iPhone 15 Pro Max', '256GB', 'Titanio Natural', 'nuevo', 100, '351234567890123', 1, 4500000.00, 5800000.00, 'disponible'),
('iPhone 15 Pro', '128GB', 'Titanio Negro', 'nuevo', 100, '351234567890124', 1, 4000000.00, 5200000.00, 'disponible'),
('iPhone 15', '128GB', 'Rosa', 'nuevo', 100, '351234567890125', 2, 3200000.00, 4200000.00, 'disponible'),
('iPhone 14 Pro Max', '256GB', 'Morado Oscuro', 'nuevo', 100, '351234567890126', 2, 3800000.00, 4800000.00, 'disponible'),
('iPhone 14', '128GB', 'Azul', 'usado', 95, '351234567890127', 3, 2500000.00, 3300000.00, 'disponible'),
('iPhone 13 Pro', '256GB', 'Verde Alpino', 'usado', 90, '351234567890128', 3, 2800000.00, 3600000.00, 'disponible'),
('iPhone 13', '128GB', 'Medianoche', 'usado', 88, '351234567890129', 1, 2200000.00, 2900000.00, 'disponible');

-- ========================================
-- VISTAS PARA REPORTES
-- ========================================

CREATE VIEW vista_ventas_detalladas AS
SELECT 
    v.id,
    v.numero_venta,
    v.fecha_venta,
    c.nombre AS cliente,
    c.cedula,
    u.nombre AS vendedor,
    v.tipo_venta,
    v.subtotal,
    v.descuento,
    v.total,
    v.forma_pago,
    v.estado,
    GROUP_CONCAT(CONCAT(i.modelo, ' ', i.capacidad, ' (', i.imei, ')') SEPARATOR ', ') AS productos
FROM ventas v
INNER JOIN clientes c ON v.cliente_id = c.id
INNER JOIN usuarios u ON v.vendedor_id = u.id
LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
LEFT JOIN iphones i ON dv.iphone_id = i.id
GROUP BY v.id;

CREATE VIEW vista_creditos_activos AS
SELECT 
    cr.id,
    cr.numero_credito,
    c.nombre AS cliente,
    c.cedula,
    c.telefono,
    cr.monto_total,
    cr.cuota_inicial,
    cr.monto_financiado,
    cr.tasa_interes,
    cr.numero_cuotas,
    cr.valor_cuota,
    cr.total_intereses,
    cr.total_pagado,
    cr.saldo_pendiente,
    cr.fecha_inicio,
    cr.fecha_primer_cuota,
    cr.estado,
    cr.dias_mora
FROM creditos cr
INNER JOIN clientes c ON cr.cliente_id = c.id
WHERE cr.estado IN ('activo', 'mora');

CREATE VIEW vista_inventario_disponible AS
SELECT 
    i.id,
    i.modelo,
    i.capacidad,
    i.color,
    i.condicion,
    i.estado_bateria,
    i.imei,
    p.nombre AS proveedor,
    p.empresa AS empresa_proveedor,
    i.precio_compra,
    i.precio_venta,
    (i.precio_venta - i.precio_compra) AS margen_ganancia,
    i.fecha_ingreso,
    i.estado
FROM iphones i
LEFT JOIN proveedores p ON i.proveedor_id = p.id
WHERE i.estado = 'disponible';

-- ========================================
-- TRIGGERS
-- ========================================

DELIMITER //

-- Trigger para actualizar el estado del iPhone al vender
CREATE TRIGGER after_venta_insert
AFTER INSERT ON detalle_ventas
FOR EACH ROW
BEGIN
    DECLARE tipo_venta_actual VARCHAR(20);
    
    SELECT tipo_venta INTO tipo_venta_actual
    FROM ventas WHERE id = NEW.venta_id;
    
    IF tipo_venta_actual = 'contado' THEN
        UPDATE iphones SET estado = 'vendido', fecha_venta = NOW()
        WHERE id = NEW.iphone_id;
    ELSE
        UPDATE iphones SET estado = 'en_credito', fecha_venta = NOW()
        WHERE id = NEW.iphone_id;
    END IF;
END//

-- Trigger para actualizar totales del cliente
CREATE TRIGGER after_venta_cliente
AFTER INSERT ON ventas
FOR EACH ROW
BEGIN
    UPDATE clientes 
    SET total_compras = total_compras + NEW.total
    WHERE id = NEW.cliente_id;
END//

-- Trigger para actualizar saldo de crédito al hacer un pago
CREATE TRIGGER after_pago_credito
AFTER INSERT ON pagos_credito
FOR EACH ROW
BEGIN
    UPDATE creditos 
    SET 
        total_pagado = total_pagado + NEW.monto_pago,
        saldo_pendiente = saldo_pendiente - NEW.monto_pago,
        estado = CASE 
            WHEN (saldo_pendiente - NEW.monto_pago) <= 0 THEN 'pagado'
            ELSE estado
        END
    WHERE id = NEW.credito_id;
END//

-- Trigger para actualizar saldo del apartado al hacer abono
CREATE TRIGGER after_abono_apartado
AFTER INSERT ON abonos_apartado
FOR EACH ROW
BEGIN
    UPDATE apartados 
    SET saldo_pendiente = saldo_pendiente - NEW.monto
    WHERE id = NEW.apartado_id;
END//

DELIMITER ;

-- ========================================
-- PROCEDIMIENTOS ALMACENADOS
-- ========================================

DELIMITER //

-- Procedimiento para verificar mora de créditos
CREATE PROCEDURE verificar_moras()
BEGIN
    DECLARE dias_tolerancia INT;
    
    SELECT valor INTO dias_tolerancia 
    FROM configuracion 
    WHERE clave = 'dias_mora_tolerancia';
    
    UPDATE creditos cr
    SET 
        dias_mora = GREATEST(0, DATEDIFF(CURRENT_DATE, cr.fecha_primer_cuota) - dias_tolerancia),
        estado = CASE 
            WHEN DATEDIFF(CURRENT_DATE, cr.fecha_primer_cuota) > dias_tolerancia THEN 'mora'
            ELSE 'activo'
        END
    WHERE estado = 'activo' AND saldo_pendiente > 0;
    
    UPDATE clientes c
    SET estado = 'moroso'
    WHERE id IN (
        SELECT DISTINCT cliente_id 
        FROM creditos 
        WHERE estado = 'mora'
    );
END//

-- Procedimiento para generar número de venta
CREATE PROCEDURE generar_numero_venta(OUT numero VARCHAR(20))
BEGIN
    DECLARE ultimo_numero INT DEFAULT 0;
    SELECT COALESCE(MAX(CAST(SUBSTRING(numero_venta, 5) AS UNSIGNED)), 0) INTO ultimo_numero
    FROM ventas
    WHERE numero_venta LIKE 'VTA-%';
    SET numero = CONCAT('VTA-', LPAD(ultimo_numero + 1, 6, '0'));
END//

-- Procedimiento para generar número de crédito
CREATE PROCEDURE generar_numero_credito(OUT numero VARCHAR(20))
BEGIN
    DECLARE ultimo_numero INT DEFAULT 0;
    SELECT COALESCE(MAX(CAST(SUBSTRING(numero_credito, 5) AS UNSIGNED)), 0) INTO ultimo_numero
    FROM creditos
    WHERE numero_credito LIKE 'CRE-%';
    SET numero = CONCAT('CRE-', LPAD(ultimo_numero + 1, 6, '0'));
END//

DELIMITER ;

-- ========================================
-- MENSAJE DE ÉXITO
-- ========================================
SELECT '======================================' AS '';
SELECT 'BASE DE DATOS CREADA EXITOSAMENTE' AS Mensaje;
SELECT '======================================' AS '';
SELECT '' AS '';
SELECT 'USUARIOS CREADOS:' AS '';
SELECT '- admin@goapple.com / admin123 (Administrador)' AS '';
SELECT '- vendedor@goapple.com / admin123 (Vendedor)' AS '';
SELECT '' AS '';
SELECT 'TABLAS CREADAS:' AS '';
SELECT 'usuarios, clientes, proveedores, iphones' AS '';
SELECT 'ventas, detalle_ventas, creditos, pagos_credito' AS '';
SELECT 'apartados, abonos_apartado, devoluciones' AS '';
SELECT 'comision_config, comision_pagos, notificaciones' AS '';
SELECT 'fotos_compra, fotos_venta, configuracion' AS '';
