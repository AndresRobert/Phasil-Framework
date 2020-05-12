<?php

// DEBUG
ini_set('display_errors', 1);

// DIRECTORIES
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DS);
define('API', ROOT.'api'.DS);
    define('CFG', API.'config'.DS);
        define('CORE', CFG.'Core.php');
        define('ROUTER', CFG.'Router.php');
    define('MDL', API.'models'.DS);
        define('MODEL', MDL.'Model.php');
    define('RSP', API.'responses'.DS);
        define('RESPONSE', RSP.'Response.php');
    define('KIT', API.'kits'.DS);
        define('AUTH', KIT.'Auth.php');
        define('CLIENT', KIT.'Client.php');
        define('COOKIE', KIT.'Cookie.php');
        define('DBASE', KIT.'Database.php');
        define('FILE', KIT.'File.php');
        define('SESSION', KIT.'Session.php');
        define('TEXT', KIT.'Text.php');
    define('SRC', API.'src'.DS);
    define('TMP', API.'tmp'.DS);
define('VENDOR', ROOT.'vendor'.DS);

// GLOBALS
define('ALLOWED_IMG_EXTENSIONS', ['jpeg', 'jpg', 'png']);
define('HTACCESS_FOLDER', '/api');
define('ABOUT', ['about' => ['framework' => 'Phasil v0.1.0', 'website' => 'https://phasil.acode.cl', 'contact' => 'phasil@acode.cl']]);
define('APPNAME', 'Phasil');

// ROUTES
// GET, POST, PUT, PATCH, DELETE, COPY, HEAD, OPTIONS, LINK, UNLINK, PURGE, LOCK, UNLOCK, PROPFIND, VIEW
define('ALLOWED_METHODS', ['POST', 'GET', 'PUT', 'DELETE']);
define('METHOD', strtoupper($_SERVER['REQUEST_METHOD']));
define('REQUEST', $_SERVER['REQUEST_URI']);
define('HEADERS', getallheaders());
define('BODY', json_decode(file_get_contents('php://input', 'r'), TRUE) ?? []);

// DATABASE
define('DB_HOST', 'localhost');
define('DB_NAME', 'phasil');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_TABLE_PREFIX', '');

// USE
require_once ROUTER;
require_once MODEL;
require_once RESPONSE;
require_once AUTH;
require_once CLIENT;
require_once COOKIE;
require_once DBASE;
require_once FILE;
require_once SESSION;
require_once TEXT;

// HEADERS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: ".implode(',', ALLOWED_METHODS));
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");