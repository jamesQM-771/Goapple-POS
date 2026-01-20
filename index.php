<?php
/**
 * Archivo principal del sistema
 * Punto de entrada y router
 */

require_once __DIR__ . '/config/config.php';

// Si el usuario está logueado, redirigir al dashboard
if (estaLogueado()) {
    redirect('/views/dashboard.php');
} else {
    redirect('/views/login.php');
}
