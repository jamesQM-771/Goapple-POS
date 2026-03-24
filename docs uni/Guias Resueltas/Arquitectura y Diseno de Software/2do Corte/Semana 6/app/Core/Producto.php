<?php
namespace Core;
class Producto {
    private $nombre;
    private $precio_unitario;

    public function __construct($n, $p) {
        $this->nombre = $n;
        $this->precio_unitario = $p;
    }
    public function getNombre() { return $this->nombre; }
    public function getPrecio() { return $this->precio_unitario; }
}
?>