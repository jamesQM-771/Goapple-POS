# ✅ SISTEMA DE FOTOS - ARREGLADO

## 🔧 Problemas Identificados y Solucionados

### 1. **Función JavaScript Incorrecta**
**Problema:** 
- El componente `fotos-upload.php` exponía la función como `obtenerFotosCargadas + $id_zona`
- Cuando `$id_zona = 'venta'`, la función era `obtenerFotosCargadasVenta` (sin guión)
- El formulario buscaba `obtenerFotosCargadas_venta` (con guión)

**Solución:** 
- Cambié la exponencia a: `window.obtenerFotosCargadas_<?php echo $id_zona; ?>`
- Ahora la función se llama correctamente: `obtenerFotosCargadas_venta`

### 2. **Carpetas de Upload Faltantes**
**Problema:** 
- Las carpetas `uploads/ventas` y `uploads/compras` no existían
- El servidor no podía guardar las fotos

**Solución:** 
- Creé las carpetas `/uploads/ventas` y `/uploads/compras`
- Creé `.htaccess` para proteger los uploads

### 3. **Falta de Logging**
**Problema:** 
- No había forma de saber qué fallaba en el API

**Solución:** 
- Agregué `error_log()` para rastrear requests y respuestas
- Ahora puedes ver qué pasa en `php_errors.log`

## 📁 Estructura de Carpetas

```
/uploads/
├── ventas/       ← Fotos de ventas
├── compras/      ← Fotos de compras
└── .htaccess     ← Protección de seguridad
```

## 🔄 Cómo Funciona Ahora

### 1. **Usuario carga fotos en el formulario**
```javascript
// El componente fotos-upload.php recoge las fotos
const fotosCargadas = window.obtenerFotosCargadas_venta();
// Array con {archivo, descripcion, preview}
```

### 2. **Se crea la venta primero**
```php
POST /views/ventas/nueva.php
Respuesta: {success: true, venta_id: 123}
```

### 3. **Después se suben las fotos**
```javascript
for (let foto of fotosCargadas) {
    POST /controllers/fotos-api.php
    Data: {
        accion: 'upload_venta',
        venta_id: 123,
        archivo: File object,
        descripcion: 'Descripción'
    }
}
```

### 4. **El API procesa y guarda**
- Valida el archivo (tipo, tamaño)
- Lo guarda en `/uploads/ventas/`
- Lo registra en la BD (fotos_venta)
- Retorna confirmación

## 🧪 Probar el Sistema

1. **Via Web Interface:**
   - Ve a Nueva Venta
   - Arrastra fotos al área designada
   - Completa la venta
   - Las fotos se subirán automáticamente

2. **Via Test Page:**
   - Abre `/test-fotos.html`
   - Carga una foto
   - Verás la respuesta del API

3. **Ver Logs:**
   - Abre `php_errors.log` en `/logs` o la carpeta de logs de PHP
   - Verás qué sucedió en cada request

## ✅ Checklist de Verificación

- [x] Componente fotos-upload.php carga correctamente
- [x] Función JavaScript se expone con el nombre correcto
- [x] Carpetas `/uploads/ventas` y `/uploads/compras` existen
- [x] API fotos-api.php procesa requests POST
- [x] Archivos se guardan en la carpeta correcta
- [x] Base de datos registra las fotos
- [x] Logging permite ver errores

## 🐛 Si Aún No Funciona

### Problema: "Error al guardar el archivo"
1. Verifica permisos: `chmod 755 uploads/ventas uploads/compras`
2. Revisa logs: ve `/logs` o `php_errors.log`
3. Verifica tamaño: máximo 5MB por foto
4. Verifica tipo: solo JPG, PNG, GIF, WebP

### Problema: "Venta inválida"
1. Asegúrate de que `venta_id` es > 0
2. Verifica que la venta se creó correctamente en BD

### Problema: No se ven las fotos
1. Abre la consola del navegador (F12)
2. Ve qué retorna el API
3. Revisa logs del servidor

## 📝 Estructura del Archivo Foto Guardado

```
uploads/
└── ventas/
    └── foto_65a2b4c1e3f2c.jpg
    └── foto_65a2b4d9e4g3d.png
```

Nombre único generado con: `uniqid('foto_') . '.' . extension`

## 🔐 Seguridad

El `.htaccess` en `/uploads/` bloquea:
- Ejecución de PHP
- Scripts JS
- Acceso a archivos ejecutables
- Solo permite descargar imágenes

