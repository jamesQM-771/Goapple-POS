<?php
namespace Core;

class Producto {
    private $id;
    private $modelo;
    private $capacidad;
    private $color;
    private $condicion;
    private $imei;
    private $precioCompra;
    private $precioVenta;

    public function __construct($modelo, $capacidad, $color, $condicion, $imei, $precioCompra, $precioVenta) {
        $this->modelo = $modelo;
        $this->capacidad = $capacidad;
        $this->color = $color;
        $this->condicion = $condicion;
        $this->imei = $imei;
        $this->precioCompra = $precioCompra;
        $this->precioVenta = $precioVenta;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getModelo() { return $this->modelo; }
    public function getCapacidad() { return $this->capacidad; }
    public function getColor() { return $this->color; }
    public function getCondicion() { return $this->condicion; }
    public function getImei() { return $this->imei; }
    public function getPrecioCompra() { return $this->precioCompra; }
    public function getPrecioVenta() { return $this->precioVenta; }

    public function toArray() {
        return [
            'modelo' => $this->modelo,
            'capacidad' => $this->capacidad,
            'color' => $this->color,
            'condicion' => $this->condicion,
            'imei' => $this->imei,
            'precio_compra' => $this->precioCompra,
            'precio_venta' => $this->precioVenta,
        ];
    }
}
