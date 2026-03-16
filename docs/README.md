# GOAPPLE POS - Documentacion Real del Repositorio

Este documento describe el estado real del proyecto en este repositorio (`C:\laragon\www\goapple`).

## Stack

- Backend: PHP 8+
- DB: MySQL/MariaDB (PDO)
- Frontend: HTML, CSS, JS, Bootstrap
- Arquitectura: MVC

## URL local actual

- `http://localhost/goapple`

## Estructura principal

- `config/`
- `controllers/`
- `models/`
- `views/`
- `assets/`
- `sql/`
- `tools/`
- `docs/`
- `docs uni/`

## Modulos visibles en repo

- Inventario
- Ventas
- Creditos
- Clientes
- Proveedores
- Reportes
- Usuarios
- Apartados
- Devoluciones
- Comisiones
- Vendedores

## Archivos clave

- Entrada: `index.php`
- API: `controllers/api.php`
- API fotos: `controllers/fotos-api.php`
- Esquema DB: `sql/database.sql`

## Notas de alcance

- Este repositorio no incluye implementacion activa de CSRF token.
- La generacion PDF no esta integrada como dependencia de composer en este repo; existen vistas relacionadas con factura y documentacion de referencia.
- La documentacion de materia cliente-servidor esta en `docs uni/`.

## Credenciales por defecto (segun docs internas)

- Email: `admin@goapple.com`
- Password: `admin123`

Cambiar la password en entorno real.
