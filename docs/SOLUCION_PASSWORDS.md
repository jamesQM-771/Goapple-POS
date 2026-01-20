# 🔐 Solución: Problemas con Contraseñas

## ❌ Problema
Los hashes de contraseñas en la base de datos no son válidos y no permiten iniciar sesión.

## ✅ Solución Rápida

### Opción 1: Actualizar contraseñas existentes (RECOMENDADO)

1. **Accede a tu servidor** vía navegador:
   ```
   https://goapple.webexperiencess.com/actualizar_passwords.php
   ```

2. **Verás un mensaje de confirmación** indicando que las contraseñas fueron actualizadas

3. **Inicia sesión** con:
   - **Email:** admin@goapple.com
   - **Contraseña:** Admin123

4. **IMPORTANTE:** Elimina el archivo después de usarlo:
   - Borra: `actualizar_passwords.php`

### Opción 2: Resetear usuarios completamente

Si la Opción 1 no funciona:

1. **Accede a:**
   ```
   https://goapple.webexperiencess.com/reset_usuarios.php
   ```

2. Este script eliminará y recreará los usuarios con contraseñas correctas

3. **Elimina el archivo** después: `reset_usuarios.php`

### Opción 3: Actualizar desde phpMyAdmin

1. Accede a **phpMyAdmin** en tu hosting
2. Selecciona la base de datos `giorgiju_goapple_pos`
3. Abre la tabla **usuarios**
4. Haz clic en **Editar** en el usuario admin
5. En el campo **password**, pega este hash:
   ```
   $2y$10$Xw7.rEVmxrSfJmh8RJPzAexb8Yh4.h3h7Np7lCjGhxP3RxKfQsYqK
   ```
6. Guarda los cambios
7. Inicia sesión con:
   - Email: admin@goapple.com
   - Contraseña: Admin123

### Opción 4: Ejecutar SQL directamente

En phpMyAdmin, ejecuta este SQL:

```sql
-- Generar hash válido para Admin123
UPDATE usuarios 
SET password = '$2y$10$Xw7.rEVmxrSfJmh8RJPzAexb8Yh4.h3h7Np7lCjGhxP3RxKfQsYqK'
WHERE email IN ('admin@goapple.com', 'vendedor@goapple.com');
```

## 📝 ¿Por qué pasó esto?

El hash original en el archivo SQL era un hash genérico de Laravel que no corresponde a "Admin123". Los nuevos hashes son generados correctamente con `password_hash()` de PHP.

## 🔍 Verificar que funciona

1. Ve a: https://goapple.webexperiencess.com/
2. Usa las credenciales:
   - **Email:** admin@goapple.com
   - **Contraseña:** Admin123
3. Deberías poder acceder al dashboard

## 🛡️ Seguridad

**Después de solucionar el problema:**
1. ✅ Elimina los archivos de utilidad:
   - `actualizar_passwords.php`
   - `reset_usuarios.php`
   - `generar_hash.php`

2. ✅ Cambia la contraseña desde el sistema:
   - Ve a tu perfil
   - Cambia "Admin123" por una contraseña segura

## 📞 ¿Aún tienes problemas?

Si ninguna opción funciona:

1. Verifica que la base de datos esté correctamente configurada en `config/database.php`
2. Revisa los logs: `logs/error.log`
3. Asegúrate que PHP tenga la extensión `openssl` habilitada
4. Contacta a tu proveedor de hosting si persiste el problema

---

**Contraseña actual válida:** Admin123  
**Hash válido:** `$2y$10$Xw7.rEVmxrSfJmh8RJPzAexb8Yh4.h3h7Np7lCjGhxP3RxKfQsYqK`
