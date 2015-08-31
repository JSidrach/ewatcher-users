# EWatcher Users

Create users of the platform *emoncms* with predefined inputs and feeds.
Two datasets are included, `pv` and `consumption`, useful for the [EWatcher](https://github.com/jsidrach/ewatcher) module.

Install & Configure
-------------------
* **Note**: it is necessary to have *emoncms* installed (>= v9)
* Change path to the *Apache* folder (usually `/var/www/html/`)
* Clone the repository via `git clone https://github.com/jsidrach/ewatcher-users/`
* Change path to the cloned repository folder
* Copy `default.settings.php` to `settings.php` (`cp default.settings.php settings.php`)
* Edit `settings.php` and set the database parameters
* Copy `default.htpasswd` to `.htpasswd`
* Edit `.htpasswd`, replacing the line with a new generated password (the default is `admin/admin`), using a tool like [this one](http://www.htaccesstools.com/htpasswd-generator/)
* Copy `default.htaccess` to `.htaccess`
* Edit `.htaccess`, if needed, setting the `.htpasswd` path
* Edit `/etc/apache2/apache2.conf`, leaving the web folder directory like:

~~~~
<Directory /var/www/html>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
~~~~

* Restart *Apache* via `sudo apachectl restart`
* Ready to go! Access with your browser to `YOUR_SERVER_IP/ewatcher-users`

How to add more user profiles
-----------------------------
Each user profile contains three definition files:

* `<profileId>_feeds.json`
* `<profileId>_inputs.json`
* `<profileId>_processes.json`

These files are stored in the `data/` folder.
A *JSON* schema for each type of file if provided in the `data/` folder too.
After adding the three files to the `data/` folder, edit `settings.php` adding a new element to the `$user_profiles` variable (`"<profileId>" => "Short description"`)

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
