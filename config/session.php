<?php
/**
 * Gestión de sesiones
 * Sistema POS GOapple
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    // Configurar parámetros de sesión antes de iniciarla
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
    
    session_start();
}

/**
 * Función para iniciar sesión de usuario
 */
function login_user($user_data) {
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['nombre'] = $user_data['nombre'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['rol'] = $user_data['rol'];
    $_SESSION['login_time'] = time();
    
    // Regenerar ID de sesión por seguridad
    session_regenerate_id(true);
}

/**
 * Función para cerrar sesión
 */
function logout_user() {
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destruir la sesión
    session_destroy();
}

/**
 * Función para verificar timeout de sesión (30 minutos)
 */
function check_session_timeout() {
    $timeout = 1800; // 30 minutos en segundos
    
    if (isset($_SESSION['login_time'])) {
        $elapsed = time() - $_SESSION['login_time'];
        
        if ($elapsed > $timeout) {
            logout_user();
            return false;
        }
    }
    
    // Actualizar tiempo de actividad
    $_SESSION['login_time'] = time();
    return true;
}

/**
 * Middleware de autenticación
 */
function require_login() {
    if (!is_logged_in()) {
        set_message('Debe iniciar sesión para acceder a esta página', 'warning');
        redirect('login.php');
    }
    
    if (!check_session_timeout()) {
        set_message('Su sesión ha expirado. Por favor, inicie sesión nuevamente.', 'info');
        redirect('login.php');
    }
}

/**
 * Middleware de autorización (solo administradores)
 */
function require_admin() {
    require_login();
    
    if (!is_admin()) {
        set_message('No tiene permisos para acceder a esta página', 'danger');
        redirect('index.php');
    }
}
