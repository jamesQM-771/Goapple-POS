<?php
/**
 * Configuración General del Sistema
 * Sistema POS GoApple
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores (cambiar a false en producción)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Rutas del sistema
define('BASE_PATH', dirname(__DIR__));

// BASE_URL - usar según el entorno
define('BASE_URL', 'http://localhost/goapple');
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Inicializar directorios de uploads
function inicializar_directorios_uploads() {
    $dirs = [
        UPLOADS_PATH,
        UPLOADS_PATH . '/fotos',
        UPLOADS_PATH . '/compras',
        UPLOADS_PATH . '/ventas'
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        // Asegurar que el directorio es escribible
        if (!is_writable($dir)) {
            @chmod($dir, 0755);
        }
    }
}

// Llamar al inicializador
inicializar_directorios_uploads();

// Configuración de la aplicación
define('APP_NAME', 'GoApple POS');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Sistema de Punto de Venta para Tienda de iPhones');

// Configuración de empresa (esto también se puede cargar desde la BD)
define('EMPRESA_NOMBRE', 'GOAPPLE');
define('EMPRESA_NIT', '900123456-7');
define('EMPRESA_TELEFONO', '+57 300 123 4567');
define('EMPRESA_EMAIL', 'ventas@goapple.com');
define('EMPRESA_DIRECCION', 'Calle 123 #45-67, Bogotá, Colombia');

// Configuración de sesión
define('SESSION_TIMEOUT', 7200); // 2 horas en segundos

// Configuración de paginación
define('RECORDS_PER_PAGE', 10);

// Roles del sistema
define('ROL_ADMIN', 'administrador');
define('ROL_VENDEDOR', 'vendedor');

// Estados de clientes
define('CLIENTE_ACTIVO', 'activo');
define('CLIENTE_MOROSO', 'moroso');
define('CLIENTE_BLOQUEADO', 'bloqueado');

// Estados de iPhones
define('IPHONE_DISPONIBLE', 'disponible');
define('IPHONE_VENDIDO', 'vendido');
define('IPHONE_EN_CREDITO', 'en_credito');
define('IPHONE_APARTADO', 'apartado');

// Tipos de venta
define('VENTA_CONTADO', 'contado');
define('VENTA_CREDITO', 'credito');

// Estados de crédito
define('CREDITO_ACTIVO', 'activo');
define('CREDITO_PAGADO', 'pagado');
define('CREDITO_MORA', 'mora');
define('CREDITO_CANCELADO', 'cancelado');

// Configuración de créditos
define('TASA_INTERES_DEFAULT', 3.5); // Porcentaje mensual
define('DIAS_MORA_TOLERANCIA', 5);
define('PENALIZACION_MORA', 5); // Porcentaje

// Configuración de comisiones
define('COMISION_DEFAULT_PCT', 2.0); // Porcentaje por defecto
define('COMISION_META_MENSUAL', 10000000); // Meta mensual
define('COMISION_BONO_META', 200000); // Bono por meta

// Formatos de moneda
define('MONEDA_SIMBOLO', '$');
define('MONEDA_CODIGO', 'COP');
define('MONEDA_DECIMALES', 0);

/**
 * Función para formatear números como moneda colombiana
 */
function formatearMoneda($valor) {

    if ($valor === null || $valor === '') {
        $valor = 0;
    }

    return MONEDA_SIMBOLO . ' ' . number_format((float)$valor, MONEDA_DECIMALES, ',', '.');
}

/**
 * Función para formatear fechas
 */
function formatearFecha($fecha, $formato = 'd/m/Y') {
    if (empty($fecha)) return '';
    $timestamp = is_numeric($fecha) ? $fecha : strtotime($fecha);
    return date($formato, $timestamp);
}

/**
 * Función para formatear fecha y hora
 */
function formatearFechaHora($fecha, $formato = 'd/m/Y H:i') {
    if (empty($fecha)) return '';
    $timestamp = is_numeric($fecha) ? $fecha : strtotime($fecha);
    return date($formato, $timestamp);
}

/**
 * Función para sanitizar entrada de datos
 */
function sanitizar($data) {
    if (is_array($data)) {
        return array_map('sanitizar', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Función para validar email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Función para generar número consecutivo
 */
function generarConsecutivo($prefijo, $ultimo_numero) {
    $siguiente = $ultimo_numero + 1;
    return $prefijo . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
}

/**
 * Función para calcular edad a partir de fecha de nacimiento
 */
function calcularEdad($fecha_nacimiento) {
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $hoy->diff($nacimiento)->y;
}

/**
 * Función para verificar si el usuario está logueado
 */
function estaLogueado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Función para obtener el usuario actual
 */
function usuarioActual() {
    return estaLogueado() ? [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'],
        'email' => $_SESSION['usuario_email'],
        'rol' => $_SESSION['usuario_rol']
    ] : null;
}

/**
 * Función para verificar si el usuario es admin
 */
function esAdmin() {
    return estaLogueado() && $_SESSION['usuario_rol'] === ROL_ADMIN;
}

/**
 * Función para verificar si el usuario es vendedor
 */
function esVendedor() {
    return estaLogueado() && $_SESSION['usuario_rol'] === ROL_VENDEDOR;
}

/**
 * Función para redireccionar
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Función para mostrar alertas
 */
function setFlashMessage($tipo, $mensaje) {
    $_SESSION['flash_message'] = [
        'tipo' => $tipo, // success, error, warning, info
        'mensaje' => $mensaje
    ];
}

/**
 * Función para obtener y limpiar alertas
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Autoload de clases
 */
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/models/',
        BASE_PATH . '/controllers/',
        BASE_PATH . '/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Verificar timeout de sesión
 */
function verificarTimeoutSesion() {
    if (estaLogueado()) {
        if (isset($_SESSION['ultima_actividad'])) {
            $inactivo = time() - $_SESSION['ultima_actividad'];
            if ($inactivo > SESSION_TIMEOUT) {
                session_unset();
                session_destroy();
                setFlashMessage('warning', 'Tu sesión ha expirado por inactividad.');
                redirect('/login.php');
            }
        }
        $_SESSION['ultima_actividad'] = time();
    }
}

// Verificar timeout en cada carga de página
verificarTimeoutSesion();
