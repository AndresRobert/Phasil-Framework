# Phasil: PHP API Simple Layout
This is simpler than a microframework, it's just a layout for rapid backend API development.

Don't get lost on hard to code REST definitions, you just need to define an endpoint and 
write a response for it. Easy as that!.

New ERA model for API development (ERA: Endpoint-Response API).

You need to connect a database? Sure, MySQL configuration and JWT security is out of the box.
The project has no deep roots so no limits on what you are able to modify.

Phasil stands for PHp Api SImple Layout, but also is pronounced like the Spanish word "fácil" 
that means easy.  

## Minimum Requirements
* Apache server
* MySQL (by default, can be changed)
* PHP 7+

## How to use
On "/api/index.php" set a new route (method, endpoint, response):
````php
<?php
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
<?php
// DATABASE
define('DB_HOST', 'localhost');
define('DB_NAME', 'phasil');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_TABLE_PREFIX', '');
````
Create a table (MySQL):
````mysql
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id int(255) NOT NULL AUTO_INCREMENT,
  user_name varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  full_name varchar(255) DEFAULT NULL,
  device varchar(255) DEFAULT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modified timestamp NULL DEFAULT NULL,
  deleted timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY user_name (user_name),
  ADD UNIQUE KEY email (email);
````
Add some rows:
````mysql
INSERT INTO users (id, user_name, password, email) VALUES
(1, 'andres', '$2y$10$...', 'andres@acode.cl'),
(2, 'robert', '$5t$87$...', 'robert@acode.cl');
````
Create/Use the corresponding model file "/api/models/UsersModel.php" and extend the basic Model 
(the important part is to define the table_name):
````php
<?php
class Users extends Model {
    public function __construct () {
        $this->table = 'users';
        parent::__construct();
    }
}
````
As you can guess, you can obviously redefine everything, add more methods, etc... 
you're welcome #Hela'sVoice.

We can define a second endpoint in the "/api/index.php" file:
````php
<?php
Route::Create('POST', '/listUsers', 'users/list');
````
Add the class and method (api/models/UsersModel.php):
````php
<?php

require_once MDL.'UserModel.php';

class Users extends Response {
    
    /**
     * List all users
     *
     * @param array $filters: passed by payload ;)
     * @return array
     */
    function list (array $filters = []): array {
        return (new User())->filter(['user_name', 'email'], $filters);
    }

}
````

Call the endpoint (you can add a filter if you want):
````bash
curl --location --request POST 'https://www.yourwebsite.dev/api/users/list' \
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

> That's way too unsafe! Anyone could access users data!!

Sure! but we got your back also out from the box!

## JWT - Out from the Box
JWT Library (firebase/php-jwt) is there pre-implemented on the Auth kit.

First, you need to change the JWT_SECRET on "/api/config/Core.php" or else 
everyone who uses this layout will "know your secret":
````php
<?php
// JWT
// You must change this JWT_SECRET for your project
define('JWT_SECRET', 'wLdkrBuQ36auUFzEd2mv9KyznwtLgaBXgoUUAMJvSXGN4uvy3OjnBUDbgT-gh27fl3AmDS2SdnVZ5KnHcWrWFrd8C13RXIbso4tDg1BVOEVgTZnUxIdiDm0csn--HRqEG-xbB8RZokBZeHTq53Uh0TkuUSPeb_tkfuhmYttIHZU');
define('JWT_ISSUER', 'PHASIL');
define('JWT_AUDIENCE', 'MY_AUDIENCE');
define('JWT_NOT_BEFORE', 5); // delay in seconds
define('JWT_EXPIRE', 600); // duration in seconds
````
Obviouly you can change everything.

Second, make your response authorization safe (on api/models/UsersModel.php):
````php
<?php
function list (array $filters = []): array {
    $validate = Auth::JWTValidate();
    if ($validate['status'] === 'success') {
        return (new User())->filter(['user_name', 'email'], $filters);
    }
    return ['response_code' => 401];
}
````
And call the endpoint again to get:
````json
{
    "status": "Unauthorized",
    "response": {
        "status": "fail",
        "id": "-1",
        "message": "Not Authorized",
        "response_code": 401
    }
}
````
Great!, protected already!, lets try to call it using a token:
````bash
curl --location --request POST 'localhost/api/users/list' \
--header 'Authorization: Bearer eyJ0eXAiOiJ...' \
--header 'Content-Type: application/json' \
--data-raw '{
	"id": "1"
}'
````
And you´ll get:
````json
{
    "status": "Unauthorized",
    "response": {
        "status": "fail",
        "id": "-1",
        "message": "Expired token",
        "response_code": 401
    }
}
````
Sorry, just kidding, I was using an expired token:
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
That's a lot better (actually the same as before the security extras)

> Wait a second, something feels wrong... how did you get that token? 

You are right, I was trying to avoid the full documentation (as most programmers do).

Third, we should add a login to get the token right? (api/models/UsersModel.php):
````php
<?php

require_once MDL.'UserModel.php';

class Users extends Response {
    
    /** List all users */
    function list (array $filters = []): array {...}
    
    public function login (array $userPayload): array {
        $User = new User();
        if ($User->readBy(['user_name' => $userPayload['user_name']])) {
            if (Auth::Match($userPayload['password'], $User->get('password'))) {
                $tokenData = Auth::JWToken($User);
                return [
                    'response_code' => 200,
                    'token' => $tokenData['token']
                ];
            }
        }
        return [
           'response_code' => 400,
           'token' => 'notoken'
       ];
    }

}
````
Call it:
````bash
curl --location --request POST 'localhost/api/login' \
--header 'Content-Type: application/json' \
--data-raw '{
	"id": "1"
}'
````
and get:
````json
{
    "status": "OK",
    "response": {
        "response_code": 200,
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJQSEFTSUwiLCJhdWQiOiJUSEVfQVVESUVOQ0UiLCJpYXQiOjE1ODkzMjE2MTYsIm5iZiI6MTU4OTMyMTYyMSwiZXhwIjoxNTg5MzIyMjE2LCJkYXRhIjp7ImlkIjoiNiIsInVzZXJfbmFtZSI6ImJhcmJhcmEiLCJlbWFpbCI6ImJhcmJhcmFAYWNvZGUuY2wifX0.dC0RzJAGg38lxD7c1AIBmKRCMh8I1ffcjL15JGggiTc"
    }
}
````
That's it! Notice that the token came back by one line of code: `Auth::JWToken($User)`

>I wanna know more?

## FAQ
* How does the response get rendered?
    * This line in the index.php gets the job done by getting the METHOD used, the REQUESTed endpoint and the BODY payload:
    ````php
    <?php
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
