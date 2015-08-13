# Creación Automática de Usuarios
Sistema para crear, mediante el navegador, usuarios en la plataforma *emoncms*, asignándoles automáticamente alguno de los perfiles disponibles (Autoconsumo FV o Consumo Eléctrico)

### Instalación y Configuración
* **NOTA**: es necesario haber instalado *emoncms* previamente
* Situarse en la carpeta de *Apache* (`/var/www/html/`)
* Clonar el repositorio mediante `git clone https://USUARIO@bitbucket.org/ismsolar/creacion-usuarios.git`, sustituyendo `USUARIO` por tu nombre de usuario
* Situarse en la carpeta del proyecto (`/var/www/html/creacion-usuarios/`)
* Copiar el archivo `default.settings.php` a `settings.php` (`cp default.settings.php settings.php`)
* Editar el archivo `settings.php` estableciendo los parámetros de conexión a la base de datos
* Copiar el archivo `default.htpasswd` a `.htpasswd` (`cp default.htpasswd .htpasswd`)
* Las credenciales por defecto para acceder a la página son `admin/admin`
* Para cambiar la contraseña de acceso, editar el archivo `.htpasswd` reemplazando la línea por una generada, por ejemplo, [aquí](http://www.htaccesstools.com/htpasswd-generator/)
* Editar el archivo `/etc/apache2/apache2.conf`, quedando la parte del directorio web así:

~~~~
<Directory /var/www/html>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
~~~~

* Reiniciar *Apache* ejecutando `sudo apachectl restart`

### Utilización
Abrir en el navegador la dirección del servidor en el que se ha instalado el proyecto seguida de `/creacion-usuarios/`

### Actualización
* Situarse en la carpeta `/var/www/html/creacion-usuarios/` y ejecutar `git pull`
* Descartar los cambios locales en caso de haber discrepancias

### Librerías externas
Se utilizan las siguientes librerías de terceros:

* [SweetAlert](http://t4t5.github.io/sweetalert/): notificaciones
* [registration-form](http://www.cssflow.com/snippets/registration-form): estilo del formulario

### Tareas pendientes
* Funciones en query.php del perfil Autoconsumo FV
* Funciones en query.php del perfil Consumo Eléctrico
