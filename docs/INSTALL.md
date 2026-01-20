# 📖 MANUAL DE INSTALACIÓN - GOAPPLE POS

Guía completa para instalar el Sistema de Punto de Venta para Tiendas de iPhones.

---

## 📋 REQUISITOS PREVIOS

### Software Necesario

1. **XAMPP** (Incluye Apache, MySQL y PHP)
   - Descargar desde: https://www.apachefriends.org/
   - Versión recomendada: 8.0 o superior

2. **Navegador Web Moderno**
   - Google Chrome (recomendado)
   - Mozilla Firefox
   - Safari
   - Microsoft Edge

### Requisitos del Servidor

- **PHP:** 8.0 o superior
- **MySQL:** 5.7+ o MariaDB 10.3+
- **Apache:** 2.4+ con mod_rewrite
- **Memoria PHP:** Mínimo 256MB
- **Espacio en disco:** Mínimo 500MB

### Extensiones PHP Requeridas

- PDO
- PDO_MySQL
- mbstring
- json
- session
- gd (para generación de PDFs con imágenes)

---

## 🚀 INSTALACIÓN PASO A PASO

### PASO 1: Instalar XAMPP

#### En Windows:

1. Descargar el instalador desde https://www.apachefriends.org/
2. Ejecutar el instalador (xampp-windows-x64-x.x.x-installer.exe)
3. Seguir el asistente de instalación
4. Instalar en la ruta por defecto: `C:\xampp`
5. Iniciar el Panel de Control de XAMPP
6. Activar los servicios Apache y MySQL

#### En macOS:

1. Descargar el instalador para Mac
2. Montar el archivo DMG descargado
3. Arrastrar XAMPP a la carpeta Aplicaciones
4. Abrir XAMPP desde Aplicaciones
5. Hacer clic en "Start" en Apache y MySQL

#### En Linux:

```bash
# Descargar el instalador
wget https://www.apachefriends.org/xampp-files/8.0.x/xampp-linux-x64-8.0.x-installer.run

# Dar permisos de ejecución
chmod +x xampp-linux-x64-8.0.x-installer.run

# Ejecutar instalador
sudo ./xampp-linux-x64-8.0.x-installer.run

# Iniciar servicios
sudo /opt/lampp/lampp start
```

---

### PASO 2: Copiar los Archivos del Sistema

#### En Windows:

1. Copiar la carpeta `GOAPPLE2` completa
2. Pegarla en: `C:\xampp\htdocs\`
3. Ruta final: `C:\xampp\htdocs\GOAPPLE2`

#### En macOS:

1. Copiar la carpeta `GOAPPLE2`
2. Pegarla en: `/Applications/XAMPP/xamppfiles/htdocs/`
3. Ruta final: `/Applications/XAMPP/xamppfiles/htdocs/GOAPPLE2`

#### En Linux:

```bash
sudo cp -r GOAPPLE2 /opt/lampp/htdocs/
sudo chmod -R 755 /opt/lampp/htdocs/GOAPPLE2
sudo chown -R daemon:daemon /opt/lampp/htdocs/GOAPPLE2
```

---

### PASO 3: Crear la Base de Datos

#### Opción 1: Usar phpMyAdmin (Recomendado para principiantes)

1. Abrir el navegador y visitar: `http://localhost/phpmyadmin`
2. Hacer clic en la pestaña "**SQL**"
3. Copiar todo el contenido del archivo `database.sql`
4. Pegarlo en el área de texto de phpMyAdmin
5. Hacer clic en el botón "**Continuar**" o "**Go**"
6. Esperar a que se ejecute el script (puede tardar unos segundos)
7. Verificar que aparezca el mensaje de éxito

#### Opción 2: Usar Línea de Comandos (Recomendado para usuarios avanzados)

**En Windows:**

```bash
# Abrir CMD como Administrador
cd C:\xampp\mysql\bin

# Conectar a MySQL
mysql.exe -u root -p

# Cuando pida contraseña, presionar Enter (por defecto no hay contraseña)

# Una vez dentro de MySQL, ejecutar:
source C:\xampp\htdocs\GOAPPLE2\database.sql;

# Verificar que se creó la base de datos
SHOW DATABASES;

# Salir
exit;
```

**En macOS/Linux:**

```bash
# Navegar al directorio del proyecto
cd /Applications/XAMPP/xamppfiles/htdocs/GOAPPLE2

# Importar la base de datos
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p < database.sql

# En Linux:
sudo /opt/lampp/bin/mysql -u root -p < database.sql
```

---

### PASO 4: Configurar la Conexión a la Base de Datos

1. Abrir el archivo: `config/database.php`
2. Editar las credenciales si es necesario:

```php
private $host = "localhost";       // Dejar como localhost
private $db_name = "goapple_pos";  // Nombre de la BD
private $username = "root";         // Usuario de MySQL
private $password = "";             // Contraseña (vacío por defecto en XAMPP)
```

**IMPORTANTE:** Si configuraste una contraseña para MySQL, actualízala aquí.

---

### PASO 5: Configurar las URLs del Sistema

1. Abrir el archivo: `config/config.php`
2. Ajustar la URL base según tu configuración:

```php
// Si instalaste en htdocs directamente:
define('BASE_URL', 'http://localhost/GOAPPLE2');

// Si instalaste en una subcarpeta:
define('BASE_URL', 'http://localhost/tu-carpeta/GOAPPLE2');

// Si usas un dominio personalizado:
define('BASE_URL', 'http://tu-dominio.com');
```

---

### PASO 6: Configurar Permisos (Solo Linux/macOS)

```bash
# Dar permisos de escritura a la carpeta uploads
sudo chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/GOAPPLE2/uploads

# En Linux:
sudo chmod -R 777 /opt/lampp/htdocs/GOAPPLE2/uploads

# Dar permisos al archivo .htaccess
sudo chmod 644 /Applications/XAMPP/xamppfiles/htdocs/GOAPPLE2/.htaccess
```

---

### PASO 7: Habilitar mod_rewrite en Apache

#### En Windows:

1. Abrir: `C:\xampp\apache\conf\httpd.conf`
2. Buscar la línea: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Quitar el `#` al inicio para descomentarla
4. Buscar: `AllowOverride None`
5. Cambiarlo a: `AllowOverride All`
6. Guardar y reiniciar Apache desde el Panel de Control

#### En macOS:

1. Abrir Terminal
2. Ejecutar:
```bash
sudo nano /Applications/XAMPP/xamppfiles/etc/httpd.conf
```
3. Buscar y descomentar: `LoadModule rewrite_module modules/mod_rewrite.so`
4. Cambiar `AllowOverride None` a `AllowOverride All`
5. Guardar (Ctrl+O, Enter, Ctrl+X)
6. Reiniciar Apache desde XAMPP

#### En Linux:

```bash
sudo nano /opt/lampp/etc/httpd.conf
# Hacer los mismos cambios
sudo /opt/lampp/lampp restart
```

---

### PASO 8: Acceder al Sistema

1. Abrir el navegador web
2. Visitar: `http://localhost/GOAPPLE2`
3. Serás redirigido automáticamente a la página de login

---

### PASO 9: Primer Acceso

**Credenciales por defecto:**

- **Email:** admin@goapple.com
- **Contraseña:** admin123
- **Rol:** Administrador

**⚠️ IMPORTANTE:**

1. Cambiar la contraseña inmediatamente después del primer acceso
2. Ir a: **Perfil → Cambiar Contraseña**
3. Crear usuarios adicionales con roles apropiados

---

## 🔧 CONFIGURACIÓN ADICIONAL

### Configurar Datos de la Empresa

1. Iniciar sesión como Administrador
2. Ir a **Configuración** en el menú lateral
3. Editar:
   - Nombre de la empresa
   - NIT
   - Teléfono de contacto
   - Email corporativo
   - Dirección física
   - Logo (opcional)

### Configurar Tasas de Interés

En el mismo panel de Configuración:

- **Tasa de interés por defecto:** Ejemplo: 3.5% mensual
- **Días de tolerancia para mora:** Ejemplo: 5 días
- **Penalización por mora:** Ejemplo: 5%

### Crear Usuarios Adicionales

1. Ir a **Administración → Usuarios**
2. Hacer clic en "**Nuevo Usuario**"
3. Llenar el formulario:
   - Nombre completo
   - Email (será el usuario de acceso)
   - Contraseña
   - Rol (Administrador o Vendedor)
   - Teléfono
4. Guardar

---

## 🧪 VERIFICAR INSTALACIÓN

### Checklist de Verificación

- [ ] Apache y MySQL están corriendo
- [ ] Puedes acceder a http://localhost/GOAPPLE2
- [ ] La página de login se muestra correctamente
- [ ] Puedes iniciar sesión con las credenciales por defecto
- [ ] El dashboard muestra estadísticas (aunque estén en 0)
- [ ] Puedes navegar por todos los módulos del menú
- [ ] No hay errores en la consola del navegador (F12)

### Verificar phpMyAdmin

1. Visitar: `http://localhost/phpmyadmin`
2. Ver la base de datos `goapple_pos`
3. Verificar que existan las siguientes tablas:
   - usuarios
   - clientes
   - proveedores
   - iphones
   - ventas
   - detalle_ventas
   - creditos
   - pagos_credito
   - configuracion

---

## ❗ SOLUCIÓN DE PROBLEMAS COMUNES

### Error: "No se puede conectar a la base de datos"

**Solución:**
1. Verificar que MySQL esté corriendo en XAMPP
2. Revisar las credenciales en `config/database.php`
3. Verificar que la base de datos `goapple_pos` exista

### Error 404 al acceder al sistema

**Solución:**
1. Verificar que la carpeta esté en `htdocs`
2. Verificar que Apache esté corriendo
3. Revisar la URL en `config/config.php`

### Error: "Call to undefined function password_hash()"

**Solución:**
- Tu versión de PHP es muy antigua
- Actualizar a PHP 8.0 o superior

### El sistema no carga estilos (se ve sin diseño)

**Solución:**
1. Verificar la URL en `config/config.php`
2. Abrir F12 en el navegador y ver errores en la consola
3. Verificar que la carpeta `assets` exista

### Error: "Access forbidden" al acceder a archivos

**Solución en Linux/macOS:**
```bash
sudo chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/GOAPPLE2
```

### Los archivos no se suben

**Solución:**
```bash
# Dar permisos a la carpeta uploads
chmod 777 uploads/
```

---

## 📞 SOPORTE

Si después de seguir esta guía aún tienes problemas:

1. Verificar el archivo de logs de PHP: `xampp/php/logs/php_error_log`
2. Verificar el archivo de logs de Apache: `xampp/apache/logs/error.log`
3. Contactar soporte técnico: soporte@goapple.com

---

## 🎓 PRÓXIMOS PASOS

Después de instalar exitosamente:

1. ✅ Cambiar contraseña de administrador
2. ✅ Configurar datos de la empresa
3. ✅ Crear usuarios para vendedores
4. ✅ Agregar proveedores
5. ✅ Agregar clientes
6. ✅ Registrar inventario de iPhones
7. ✅ Realizar primera venta de prueba
8. ✅ Configurar respaldos automáticos

---

## 🔐 SEGURIDAD EN PRODUCCIÓN

### Antes de subir a un hosting/servidor en producción:

1. **Cambiar credenciales de base de datos**
2. **Habilitar HTTPS** (SSL/TLS)
3. **Cambiar contraseñas por defecto**
4. **Desactivar modo debug:**
   ```php
   // En config/config.php
   define('DEBUG_MODE', false);
   ```
5. **Configurar permisos restrictivos:**
   ```bash
   chmod 644 .htaccess
   chmod 755 uploads/
   chmod 600 config/database.php
   ```
6. **Configurar respaldos automáticos**
7. **Actualizar URL base en config/config.php**

---

## 📚 RECURSOS ADICIONALES

- [Manual de Usuario](MANUAL_USUARIO.md)
- [Preguntas Frecuentes](FAQ.md)
- [Guía de Respaldos](BACKUP.md)

---

**¡Instalación completada exitosamente! 🎉**

Ahora estás listo para usar el Sistema POS GoApple.
