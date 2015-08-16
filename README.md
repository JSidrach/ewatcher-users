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

### Funcionalidad
Este sistema permite crear, usando el navegador, usuarios en la plataforma *emoncms*, asignándoles *feeds* e *inputs* según qué perfil se seleccione (Autoconsumo FV o Consumo Eléctrico).
Para ello, consta de los siguientes ficheros principales:

* **index.php**: *front-end*, página web con un formulario para rellenar con los datos del usuario a crear
* **query.php**: *back-end*, conjunto de funciones y consultas *SQL* para crear los usuarios, *feeds* e *inputs*, así como validar el formulario
* **panel.php**: *front-end*, página web con formularios para asignar paneles a usuarios
* **panel_query.php**: *back-end*, conjunto de funciones y consultas *SQL* para establecer los paneles de los usuarios
* **defs_emoncms.php**: definiciones auxiliares, dependientes de *emoncms*, sobre los números que se asignan a las funciones, los tipos de datos y los motores utilizados

Adicionalmente, se guardan en la carpeta `data/` los archivos de definiciones de *feeds*, *inputs*, y *procesos* (conjunto de operaciones sobre cada *input*).
Estas definiciones están en formato *JSON*, y existen 3 (`_feeds.json`, `_inputs.json`, y `_processes.json`) para cada tipo de usuario (*pv* y *consumption*).
También se han incluido los esquemas de cada uno de los 3 tipos de archivo, con el prefijo `_schema`.
Se pueden validar las definiciones contra el esquema utilizando un *JSON Scheme Validator (v4)*, disponible online.

### Librerías externas
Se utilizan las siguientes librerías de terceros:

* [SweetAlert](http://t4t5.github.io/sweetalert/): notificaciones
* [Registration Form](http://www.cssflow.com/snippets/registration-form): estilo del formulario de creación de usuarios
* [Sign Up Form](http://www.cssflow.com/snippets/sign-up-form): estilo del formulario de búsqueda de usuarios
* [Settings Panel](http://www.cssflow.com/snippets/settings-panel): estilo del formulario de activación de paneles
* [jQuery 2.1.4](https://jquery.com): consultas asíncronas y manipulación del documento *HTML*

### Tareas pendientes
* Datos de procesos, probar
* paneles.php, paneles_query.php
* BD: | userid | P1 | P2 | P3 | P4 | config
