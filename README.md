EWatcher Users
--------------

Sistema para crear/borrar usuarios de la plataforma *emoncms*, y asignarles paneles dentro de alguno de los perfiles disponibles (Autoconsumo FV o Consumo Eléctrico)

### Instalación y Configuración
* **NOTA**: es necesario haber instalado *emoncms* previamente, versión 9 en adelante
* Situarse en la carpeta de *Apache* (`/var/www/html/`)
* Clonar el repositorio mediante `git clone https://USUARIO@bitbucket.org/ismsolar/gestion-usuarios.git`, sustituyendo `USUARIO` por tu nombre de usuario
* Situarse en la carpeta del proyecto (`/var/www/html/gestion-usuarios/`)
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

Install & Configure
-------------------
TODO

How to add more profiles
------------------------
TODO

Update
------
* Change path to the project folder (usually `/var/www/html/ewatcher-users/`)
* Get the newer version via `git pull`

Functionality
-------------
This projects allows to create, via browser, users of the platform *emoncms*, with predefined feeds and inputs (along with its processes).
It also allows to asign any user panels of the [EWatcher](https://github.com/jsidrach/ewatcher) *emoncms* module.
Finally, it also allows to completely remove an user and all its data.

These are the most relevant project code files:

* **index.php**: *front-end* for the user creation
* **query.php**: *back-end* for the user creation
* **panels.php**: *front-end* for the panel assignment
* **panels_query.php**: *back-end* for the panel assignment
* **delete.php**: *front-end* for the user deletion
* **delete_query.php**: *back-end* for the user deletion
* **defs_emoncms.php**: auxiliary definitions, from *emoncms* about available functions, data types and engines

Additionally, the `data\` folder contains the feeds, inputs and processes (operations over each input) definition files.
These files are in *JSON*, each profile must have three of them (`_feeds.json`, `_inputs.json` & `_processes.json`).
The default available profiles are *pv* and *consumption* (used in the [EWatcher](https://github.com/jsidrach/ewatcher) *emoncms* module).
The *JSON* files can be validated against the appropiate `_schema.json` file, using an online *JSON Scheme Validator (v4)*.

Limitations
-----------
As of now, this project have the following limitations:

* Lack of support for virtual feeds
* Lack support for schedule functions
* No spaces in a feed name
* No special characters in names or descriptions of the inputs/feeds

Screenshots
-----------
[See here](screenshots/).

License
-------
[MIT](LICENSE) - Feel free to use and edit.

Developers
----------
EWatcher is developed and has had contributions from the following people:

* [J. Sid](https://github.com/jsidrach)
* [A. Garriz Molina](alejandro.garrizmolina@gmail.com)
* [Llanos Mora](https://sites.google.com/site/llanosmora/home)

Special thanks to [ISM Solar](http://www.ismsolar.com/) for funding this project.

Contribute
----------
Submit an Issue if you encounter a bug, have any suggestion for improvements or to request new features.
Open a Pull Request for **tested** bugfixes and new features, documenting any relevant change.

Tech
----
The following libraries are used:

* [SweetAlert](http://t4t5.github.io/sweetalert/): notifications
* [Registration Form](http://www.cssflow.com/snippets/registration-form): style of the user creation page
* [Sign Up Form](http://www.cssflow.com/snippets/sign-up-form): style of the search user form
* [Settings Panel](http://www.cssflow.com/snippets/settings-panel): style of the panel assignment page
* [jQuery 2.1.4](https://jquery.com): ajax requests and DOM manipulation

### Tareas pendientes
* Nuevo README.md
  * Qué es
  * Instalación/Configuración
  * Cómo añadir perfiles, inputs, feeds, procesos
* Liberar proyecto en GitHub
