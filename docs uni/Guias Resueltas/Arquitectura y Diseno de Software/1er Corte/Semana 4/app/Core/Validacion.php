<?php
namespace Core;

class Validacion {

    public static function sanitizarEntrada($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    public static function validarEmail($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validarTelefono($telefono) {
        return preg_match('/^[0-9\+\-\s\(\)]{7,20}$/', trim($telefono)) === 1;
    }

    public static function validarCedula($cedula) {
        return preg_match('/^[0-9]{5,15}$/', trim($cedula)) === 1;
    }

    public static function validarPrecio($precio) {
        return is_numeric($precio) && $precio > 0;
    }

    public static function limpiarArray($data) {
        $limpio = [];
        foreach ($data as $key => $value) {
            $limpio[$key] = self::sanitizarEntrada($value);
        }
        return $limpio;
    }
}
