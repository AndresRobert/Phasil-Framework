<?php

// DEBUG
ini_set('display_errors', 1);

// GLOBALS
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DS);
define('API', ROOT.'api'.DS);
define('CORE', ROOT.'core'.DS);
define('KITS', CORE.'kits'.DS);
define('MODELS', API.'models'.DS);
define('RESPONSES', API.'responses'.DS);
define('SRC', API.'src'.DS);
define('IMG', SRC.'img'.DS);
define('ALLOWED_IMG_EXTENSIONS', ['jpeg', 'jpg', 'png']);
define('CONFIG', CORE.'Config.php');
define('ROUTER', CORE.'Router.php');
define('CLIENT', KITS.'Client.php');
define('COOKIE', KITS.'Cookie.php');
define('DATABASE', KITS.'Database.php');
define('FILE', KITS.'File.php');
define('PASSWORD', KITS.'Password.php');
define('SESSION', KITS.'Session.php');
define('TEXT', KITS.'Text.php');
define('MODEL', MODELS.'Model.php');
define('RESPONSE', RESPONSES.'Response.php');
define('ABOUT', ['about' => ['framework' => 'Phasil', 'website' => 'phasil.acode.cl', 'contact' => 'phasil@acode.cl']]);
define('APPNAME', 'Phasil');

// ROUTES
// GET, POST, PUT, PATCH, DELETE, COPY, HEAD, OPTIONS, LINK, UNLINK, PURGE, LOCK, UNLOCK, PROPFIND, VIEW
define('ALLOWED_METHODS', ['POST', 'GET', 'PUT', 'DELETE']);
define('METHOD', strtoupper($_SERVER['REQUEST_METHOD']));
define('REQUEST', $_SERVER['REQUEST_URI']);
define('HEADERS', getallheaders());
define('BODY', json_decode(file_get_contents('php://input', 'r'), TRUE) ?? []);

// DATABASE
define('DBHOST', 'localhost');
define('DBNAME',    'phasil');
define('DBUSERNAME',  'root');
define('DBPASSWORD',  'root');
define('DBTABLEPREFIX',   '');

// CORE
require_once ROUTER;
require_once MODEL;
require_once RESPONSE;
require_once BROWSER;
require_once COOKIE;
require_once DATABASE;
require_once FILE;
require_once PASSWORD;
require_once SESSION;
require_once TEXT;

// HEADERS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: ".implode(',', ALLOWED_METHODS));
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");