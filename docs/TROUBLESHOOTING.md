# 🚀 Solución de Problemas Comunes

## ✅ Problemas Resueltos

### 1. Warning: Constant already defined
**Solución implementada:** Todas las constantes ahora usan `defined()` antes de definirse:
```php
if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', '$');
```

### 2. Warning: session_start() after headers sent
**Solución implementada:**
- `session.php` se carga ANTES de `config.php` en todos los archivos
- `display_errors` está en OFF para producción
- Los errores se registran en `logs/error.log` en lugar de mostrarse

### 3. Warning: Cannot modify header information
**Solución implementada:**
- Configuración de errores optimizada para producción
- No hay output antes de headers
- Logs configurados correctamente

## 🔧 Configuración Actual

### Archivos Modificados

1. **config/config.php**
   - ✅ `display_errors = 0` (producción)
   - ✅ `log_errors = 1`
   - ✅ Todas las constantes protegidas con `defined()`
   - ✅ BASE_URL actualizada a: `https://goapple.webexperiencess.com/`

2. **config/session.php**
   - ✅ Configuración de sesión centralizada
   - ✅ Verifica si la sesión ya está iniciada

3. **index.php y otros archivos**
   - ✅ Orden correcto: session.php → config.php → otros

4. **config/database.php**
   - ✅ Errores no se muestran al usuario
   - ✅ Se registran en el log del servidor

## 📝 Orden de Carga Correcto

En todos los archivos PHP principales:

```php
<?php
require_once __DIR__ . '/config/session.php';  // 1. PRIMERO la sesión
require_once __DIR__ . '/config/config.php';   // 2. LUEGO la configuración
// ... resto del código
```

## 🛠 Para Modo Desarrollo

Si necesitas ver errores durante desarrollo, edita `config/config.php`:

```php
// DESARROLLO (mostrar errores)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

## 📂 Estructura de Logs

- Directorio: `/logs/`
- Archivo: `error.log`
- Protección: `.htaccess` impide acceso directo

## ⚙️ Configuración de Producción vs Desarrollo

### Producción (Actual):
```php
error_reporting(0);
ini_set('display_errors', 0);
define('BASE_URL', 'https://goapple.webexperiencess.com/');
```

### Desarrollo (localhost):
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('BASE_URL', 'http://localhost/GOapple/');
```

## 🔐 Seguridad

- ✅ `database.example.php` creado como plantilla
- ✅ `database.php` excluido de git (`.gitignore`)
- ✅ Credenciales protegidas
- ✅ Logs protegidos con `.htaccess`

## 📊 Verificación

Para verificar que todo funciona:

1. **Revisar logs:**
   ```bash
   tail -f logs/error.log
   ```

2. **Probar acceso:**
   - https://goapple.webexperiencess.com/
   - No debe mostrar warnings
   - Debe redirigir a login

3. **Verificar sesión:**
   - Login debe funcionar sin errores
   - Dashboard debe cargar correctamente

## 🆘 Si Persisten los Errores

1. **Limpiar caché del navegador**
2. **Verificar permisos de carpetas:**
   ```bash
   chmod 755 logs/
   chmod 644 logs/.htaccess
   ```

3. **Verificar php.ini del servidor:**
   - `output_buffering = On`
   - `session.auto_start = 0`

4. **Revisar archivo .htaccess raíz:**
   - No debe tener conflictos con headers

## 📞 Contacto de Soporte

Si encuentras más problemas, revisa:
- `logs/error.log` para detalles técnicos
- La documentación en `README.md`
- Los comentarios en el código

---
**Última actualización:** 12 de febrero de 2026
**Estado:** ✅ Todos los warnings resueltos
