# Phasil Framework: PHP API Simple Layout
Phasil stands for PHP Api SImple Layout, but also is pronounced like the Spanish word "fácil" that means easy.

## Endpoint-Response API
New `ERA` model for API development (ERA: Endpoint-Response API).  
Don't get lost on hard to code REST definitions, you just need to define an endpoint and write a response for it. Easy as that!.

## Databases and Security
Do you need to connect a database? Sure, MySQL configuration and JWT security is out of the box. The project has no deep roots so no limits on what you are able to modify.

## Minimum Requirements
* Apache server
* MySQL (by default, can be changed)
* PHP 7+

## How to use

### Add an ENDPOINT
In `/api/index.php` set a new route (method, endpoint, response):
````php
Route::Create('POST', '/myEndpoint', 'myClass/myMethod');
````
Create the `/api/responses/MyClassResponse.php`:
````php
<?php

use Base\Response;

class MyClass extends Response {
    public function myMethod(): array {
        return [
            'name' => 'Phasil',
            'description' => 'ERA Layout (Endpoint-Response API)',
            'link' => 'https://andresrobert.github.io/Phasil-Framework/',
            'github' => 'https://github.com/AndresRobert/Phasil-Framework'
        ];
    }
}
````

### Get the RESPONSE
Call the endpoint:
````bash
curl --location --request POST \
--header 'Content-Type: application/json' \
'https://www.mywebsite.dev/api/myEndpoint'
````
You should be seeing:
````json
{
    "status": "OK",
    "response": [
        {
            "name": "Phasil",
            "description": "ERA Layout (Endpoint-Response API)",
            "link": "https://andresrobert.github.io/Phasil-Framework/",
            "github": "https://github.com/AndresRobert/Phasil-Framework"
        }
    ]
}
````
That's it!
>"Wait a second!, you said something about some database sh... stuff!!"    
That's... true!

## Databases

### Setup

You can set your mySQL credentials in `/api/config/Core.php` file:
```php
<?php
...
// DATABASE
define('DB_HOST', 'localhost');
define('DB_NAME', 'phasil');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_TABLE_PREFIX', '');
...
```
If you are just starting, create a simple table:
```sql
CREATE TABLE users (
  id int(255) NOT NULL AUTO_INCREMENT,
  user_name varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY user_name (user_name),
  ADD UNIQUE KEY email (email);

INSERT INTO users (id, user_name, password, email) VALUES
  (1, 'andres', '$2y$10$...', 'andres@acode.cl'),
  (2, 'robert', '$5t$87$...', 'robert@acode.cl');
```

### Model

Create this file `/api/models/UsersModel.php` and extend the base model (the important part is to define the table name):
```php
<?php
use Base\Model;

class Users extends Model {
    public function __construct () {
        $this->table = 'users';
        parent::__construct();
    }
}
```
As you might have already guessed, you can obviously redefine everything, add more methods, etc... you're welcome _#Hela'sVoice_

### Access

Using the ERA model you can easily expose this data:

#### Add the users ENDPOINT
Here `/api/index.php` add a new route:
```php
Route::Create('GET', '/users', 'users/list');
```
Create the `/api/responses/UsersResponse.php` and import your model:
```php
<?php
use Base\Response;
require_once MDL.'UsersModel.php' as UserModel;

class Users extends Response {
    /**
     * List all users
     *
     * @param array $filters: passed by payload ;)
     * @return array
     */
    function list(array $filters = []): array {
        /* Use filter([SELECT], [FROM]) */
        return (new UserModel())->filter(['user_name', 'email'], $filters);
    }
}
```

#### Get Users's RESPONSE
Call the endpoint (try a filter):
```bash
curl --location --request GET \
--header 'Content-Type: application/json' \
'https://www.mywebsite.dev/api/users?id=1'
```
You should be seeing:
```json
{
    "status": "OK",
    "response": [
        {
            "user_name": "andres",
            "email": "andres@acode.cl"
        }
    ]
}
```
That's it!
> "Wait another second!, that's way too unsafe! Anyone can access users' data!!"    
That's... also true... but we got your back!

## JWT

JWT Library (`firebase/php-jwt`) is pre-implemented by using the Auth kit. Kits are just plugins wrappers or helpers for easy tooling.

### Setup

First, you need to change the `JWT_SECRET` in `/api/config/Core.php` or else everyone who uses this layout will **"know your secret"** _#ifYouKnowWhatIMean_:
```php
<?php
...
// JWT
define('JWT_SECRET', 'wLdkrBuQ3...');
define('JWT_ISSUER', 'PHASIL');
define('JWT_AUDIENCE', 'MY_AUDIENCE');
define('JWT_NOT_BEFORE', 5); // delay in seconds
define('JWT_EXPIRE', 600); // duration in seconds
...
```

### Make it safe

Make your response safe in `api/models/UsersResponse.php` using the authorization wrapper:
```php
<?php
use Base\Response;
require_once MDL.'UsersModel.php' as UserModel;

class Users extends Response {
    function list(array $filters = []): array {
        return self::RequiresAuthorization(function () use ($filters) {
            return (new User())->filter(['user_name', 'email'], $filters);
        });
    }

}
```
And call the endpoint again to get:
```json
{
    "status": "Unauthorized",
    "response": [
        {
            "status": "fail",
            "id": "-1",
            "message": "Not Authorized",
            "response_code": "401"
        }
    ]
}
```
Great!, protected already!, lets try to call it using a token:
```bash
curl --location --request GET \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer eyJ0eXAiOiJ...' \
'https://www.mywebsite.dev/api/users?id=1'
```
And you'll get:
```json
{
    "status": "Unauthorized",
    "response": [
        {
            "status": "fail",
            "id": "-1",
            "message": "Expired token",
            "response_code": "401"
        }
    ]
}
```
Sorry, just kidding! I was using a expired token XD:
```json
{
    "status": "OK",
    "response": [
        {
            "user_name": "andres",
            "email": "andres@acode.cl"
        }
    ]
}
```
That's a lot better (actually, it's the same as before but with the "security extras")
> "Wait yet another second, something feels wrong... how did you get that token?"    
You are right, I was trying to avoid the full documentation (_like most of us programmers do LOL_).

## Login Setup

Add the route:
```bash
Route::Create('POST', '/login', 'users/login');
```
In the Users response add the login method:
```php
...
public function login(array $userPayload): array {
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
...
```
Call it:
```bash
curl --location --request POST 'https://www.mywebsite.dev/api/login' \
--header 'Content-Type: application/json' \
--data-raw '{"user_name": "andres","password": "$2y$10$..."}'
```
Get:
```json
{
    "status": "OK",
    "response": {
        "response_code": 200,
        "token": "eyJ0eXAiOiJ..."
    }
}
```
That's it! Notice that the token came back by one line of code: `Auth::JWToken($User)`
> Cool! I wanna know more!!!

## FAQ

#### How does the response get rendered?
This line in the `index.php` gets the job done by getting the _METHOD_ used, the _REQUESTed_ endpoint and the _BODY_ payload:
```php
echo Route::Read(METHOD, REQUEST, BODY);
```

#### Which DB options do I have?
_Insert, Select, Update, Delete & ComplexSelect (custom queries)_ are out of the box, but you are also encouraged to add more at `api/kits/Database.php`

#### Is there a Dashboard to control global variables and configuration?
Of course! check `/api/config/Core.php out!`

#### Do I have a toolbox or something?
Sure we do! they are called Kits `/api/kits/` and there are some in there already (and more will be added): _Session, Cookie, File, Text, etc._, check them all out!.

## Troubleshooting
Check the status of your configuration calling `/api/status`
```bash
curl --location --request POST 'https://www.mywebsite.dev/api/status' \
--header 'Content-Type: application/json'
```

## Version
Version 1.0.0
See full documentation [here](https://andresrobert.github.io/Simple-Framework/)
