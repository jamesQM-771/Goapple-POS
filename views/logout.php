<?php
/**
 * Logout - Cerrar sesión
 */

require_once __DIR__ . '/../config/config.php';

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al login
setFlashMessage('success', 'Has cerrado sesión exitosamente');
redirect('/views/login.php');
