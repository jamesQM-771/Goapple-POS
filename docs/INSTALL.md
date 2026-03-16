# Manual de Instalacion (Repo actual)

## Ruta de trabajo

Este proyecto esta preparado para:

- `C:\laragon\www\goapple`

## Requisitos

- PHP 8+
- MySQL/MariaDB
- Apache (Laragon/XAMPP)

## Paso 1: Base de datos

1. Crear base de datos (ejemplo: `goapple_pos`).
2. Importar `sql/database.sql`.

## Paso 2: Configuracion

1. Editar `config/database.php` con tus credenciales.
2. Verificar en `config/config.php`:

```php
define('BASE_URL', 'http://localhost/goapple');
```

## Paso 3: Ejecutar

- Abrir: `http://localhost/goapple`

## Verificacion rapida

- Login carga correctamente.
- Dashboard abre.
- Modulos clave abren: inventario, ventas, creditos, clientes.

## Notas

- Esta guia reemplaza referencias antiguas a `goapple`.
- Si usas otra carpeta local, ajusta `BASE_URL`.

