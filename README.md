# Phasil: PHP API Simple Layout
This is more than just a basic project, it's a layout for rapid backend API PHP development.

Don't get lost on hard to code REST definitions, you just need to define an endpoint and 
write a response for it. Easy as that!.

New ERA model for API development (ERA: Endpoint Response API).

You need to connect a database? Sure, MySQL configuration out of the box.
The project has no deep roots so no limits on what you are able to modify.

Phasil stands for PHp Api SImple Layout, but also is pronounced like the Spanish word "fácil" that means easy.  

## Requirements
* Apache server
* MySQL (by default, can be changed)
* PHP 7+
* htaccess configuration (included):
````apacheconf
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)$ index.php [QSA,L]
````

## How to use
On index.php set a new route (method, endpoint, response):
````php
Route::Create('GET', '/myEndpoint', 'home/about');
````
This will define a new endpoint accessible by GET like https://www.yourwebsite.dev/myEndpoint,
and the response for it will come from the "about" method in the "home" class.

Create the /api/responses/HomeResponse.php file to define the "home" class and "about" method:
````php
<?php
class Home extends Response {
    public function about (): array {
        return [
            'name' => 'Phasil',
            'description' => 'Easy PHP ERA (Endpoint Response API) Facilitator',
            'link' => 'https://phasil.acode.cl',
            'github' => 'https://github.com/AndresRobert/Phasil-Framework'
        ];
    }
}
````

Call it by Postman, browser or just:
````bash
curl --location --request GET 'https://www.yourwebsite.dev/myEndpoint' \
--header 'Content-Type: application/json'
````

And get:
````json
{
    "status": "OK",
    "response": [
        {
            "name": "Phasil",
            "description": "Easy PHP ERA (Endpoint Response API) Facilitator",
            "link": "https://phasil.acode.cl",
            "github": "https://github.com/AndresRobert/Phasil-Framework"        
        }
    ]
}
````

Thats it!

"Wait a second!, you said something about database sh... stuff"

True! well you can actually add some more love to your project. 
You can set your credentials on the "config" file inside the "core" folder
````php
// DATABASE
define('HOST', 'localhost');
define('DBNAME', 'phasil');
define('USERNAME', 'root');
define('PASSWORD', 'root');
define('TABLE_PREFIX', '');
````
Create some table:
````mysql
CREATE TABLE `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_start` timestamp NULL DEFAULT NULL,
  `token_expire` timestamp NULL DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT NULL,
  `deleted` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
````
Add some rows:
````mysql
INSERT INTO `users` (`id`, `user_name`, `password`, `token`, `token_start`, `token_expire`, `email`, `full_name`, `device`, `created`, `modified`, `deleted`) VALUES
(1, 'andres', '$2y$10$.omT9VH0fHR/50uQbTLqf.W1B/r4JrNTmXnMDC.mOyJyLNIh6s9Bm', NULL, NULL, NULL, 'andres@acode.cl', NULL, NULL, '2020-01-02 01:23:45', NULL, NULL),
(2, 'robert', '$2y$10$DdiLEJugC7TdjAznY4AgBO0waJgUVm1Jwj4j99l393ntynI.1BVYC', NULL, NULL, NULL, 'robert@acode.cl', NULL, NULL, '2020-03-04 01:23:45', NULL, NULL);
````
Create the corresponding model on /api/models as UserModel.php and extend the basic Model (the important part is define the table name):
````php
<?php
class User extends Model {
    public function __construct () {
        parent::__construct();
        $this->table = 'users';
    }
}
````
As you can guess, you can obviously redefine everything, add more methods etc... you're welcome.

We can define a second endpoint in the index.php file:
````php
Route::Create('POST', '/users/list', 'home/users');
````
To keep it simple, I'll reuse home response handler instead of making a new one, ok?

Add the class and the method:
````php
<?php

require_once MODELS.'UserModel.php';

class Home extends Response {
    
    public function about (): array {
        return [
            'name' => 'Phasil',
            'description' => 'Easy PHP ERA (Endpoint Response API) Facilitator',
            'link' => 'https://phasil.acode.cl',
            'github' => 'https://github.com/AndresRobert/Phasil-Framework'
        ];
    }

    /**
     * Get all users
     *
     * @param array $filters: passed by payload ;)
     * @return array
     */
    function users (array $filters = []): array {
        return (new User())->filter($filters);
    }

}
````

Call the endpoint (you can add a filter if you want):
````bash
curl --location --request POST 'https://www.yourwebsite.dev/users/list' \
--header 'Content-Type: application/json' \
--data-raw '{
	"id": "1"
}'
````

And get:
````json
{
    "status": "OK",
    "response": [
        {
            "id": "1",
            "user_name": "andres",
            "password": "$2y$10$.omT9VH0fHR/50uQbTLqf.W1B/r4JrNTmXnMDC.mOyJyLNIh6s9Bm",
            "token": null,
            "token_start": null,
            "token_expire": null,
            "email": "andres@acode.cl",
            "full_name": null,
            "device": null,
            "created": "2020-05-01 00:12:29",
            "modified": null,
            "deleted": null
        }
    ]
}
````

That's it! you successfully completed your first PHP ERA API!

You wanna know more? Sure!

## FAQ
* How does the response get rendered?
    * This line in the index.php gets the job done by getting the METHOD used, the REQUESTed endpoint and the BODY payload:
    ````php
    echo Route::Read(METHOD, REQUEST, BODY);
    ````
* Which DB options do I have?
    * Insert, Select, Update, Delete & ComplexSelect (custom queries) are out of the box, but you are also encouraged to add more at /core/Database.php
* Is there a Dashboard to control global variables and configuration?
    * Of course! check /core/Config.php out!
* Do we have a toolbox, or something?
    * Sure we do! they are called Helpers (/core/Helper.php) and there are some already (and more will be added): Session, Cookie, File, Api, Text & Password, check them all out!.

## Developed by 
ACODE Design & Development 2020 [Andres Robert]

Visit https://phasil.acode.cl

## Version
0.1.0
