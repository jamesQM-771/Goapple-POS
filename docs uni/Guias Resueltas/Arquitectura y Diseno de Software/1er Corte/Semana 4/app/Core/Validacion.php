<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: james | Guía: 4 */
namespace Core;

class Validacion {
    public static function sanitizarEntrada($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}
?>