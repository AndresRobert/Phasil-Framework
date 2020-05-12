# Phasil: PHP API Simple Layout
This is simpler than a microframework, it's just a layout for rapid backend API development.

Don't get lost on hard to code REST definitions, you just need to define an endpoint and 
write a response for it. Easy as that!.

New ERA model for API development (ERA: Endpoint-Response API).

You need to connect a database? Sure, MySQL configuration out of the box.
The project has no deep roots so no limits on what you are able to modify.

Phasil stands for PHp Api SImple Layout, but also is pronounced like the Spanish word "fácil" 
that means easy.  

## Minimum Requirements
* Apache server
* MySQL (by default, can be changed)
* PHP 7+

## How to use
On /api/index.php set a new route (method, endpoint, response):
````php
Route::Create('GET', '/myEndpoint', 'home/about');
````
This will define a new endpoint accessible by GET like 
https://www.yourwebsite.dev/api/myEndpoint, and the response for it will come from the 
"about" method in the "home" class.

Create the /api/responses/HomeResponse.php file to define the "home" class and the "about" 
method:
````php
<?php
class Home extends Response {
    public function about (): array {
        return [
            'name' => 'Phasil',
            'description' => 'ERA Layout (Endpoint-Response API) Facilitator',
            'link' => 'https://phasil.acode.cl',
            'github' => 'https://github.com/AndresRobert/Phasil-Framework'
        ];
    }
}
````

Call it by Postman, browser or just:
````bash
curl --location --request GET 'https://www.yourwebsite.dev/api/myEndpoint' \
--header 'Content-Type: application/json'
````

And get:
````json
{
    "status": "OK",
    "response": [
        {
            "name": "Phasil",
            "description": "ERA Layout (Endpoint-Response API) Facilitator",
            "link": "https://phasil.acode.cl",
            "github": "https://github.com/AndresRobert/Phasil-Framework"        
        }
    ]
}
````

That's it!

>"Wait a second!, you said something about some database sh... stuff!!"

True! you can actually add some more love to your project, you can set your credentials 
on "/api/config/Core.php" file:
````php
// DATABASE
define('DB_HOST', 'localhost');
define('DB_NAME', 'phasil');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_TABLE_PREFIX', '');
````
Create a table:
````mysql
CREATE TABLE `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
);
````
Add some rows:
````mysql
INSERT INTO `users` (`id`, `user_name`, `password`, `email`) VALUES
(1, 'andres', '$2y$10$.omT9VH0fHR/50uQbTLqf.W1B/r4JrNTmXnMDC.mOyJyLNIh6s9Bm', 'andres@acode.cl'),
(2, 'robert', '$2y$10$DdiLEJugC7TdjAznY4AgBO0waJgUVm1Jwj4j99l393ntynI.1BVYC', 'robert@acode.cl');
````
Create the corresponding model file "/api/models/UserModel.php" and extend the basic Model 
(the important part is to define the table_name):
````php
<?php
class User extends Model {
    public function __construct () {
        $this->table = 'users';
        parent::__construct();
    }
}
````
As you can guess, you can obviously redefine everything, add more methods, etc... 
you're welcome.

We can define a second endpoint in the "/api/index.php" file:
````php
Route::Create('POST', '/users/listThemAll', 'home/user_list');
````
To keep it simple, I'll reuse home response handler instead of making a new one, ok?

Add the class and the method:
````php
<?php

require_once MDL.'UserModel.php';

class Home extends Response {
    
    public function about (): array {...}

    /**
     * List all users
     *
     * @param array $filters: passed by payload ;)
     * @return array
     */
    function user_list (array $filters = []): array {
        return (new User())->filter(['user_name', 'email'], $filters);
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
            "user_name": "andres",
            "email": "andres@acode.cl"
        }
    ]
}
````

That's it! you successfully completed your first ERA!

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
    * Of course! check /api/config/Core.php out!
* Do we have a toolbox, or something?
    * Sure we do! they are called Kits (/api/kits/) and there are some already (and more will be added): Session, Cookie, File, Text, etc., check them all out!.

## Developed by 
ACODE Design & Development 2020 @AndresRobert

Visit https://phasil.acode.cl

## Version
0.1.0
