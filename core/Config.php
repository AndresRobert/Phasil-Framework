<?php

// DEBUG
ini_set('display_errors', 1);

// GLOBALS
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT']);
define('API', ROOT.DS.'api'.DS);
define('CORE', ROOT.DS.'core'.DS);
define('MODELS', API.DS.'models'.DS);
define('RESPONSES', API.DS.'responses'.DS);
define('SRC', API.DS.'src'.DS);
define('IMG', SRC.'img'.DS);
define('ALLOWED_IMG_EXTENSIONS', ['jpeg', 'jpg', 'png']);
define('CONFIG', CORE.'Dashboard.php');
define('HELPER', CORE.'Helper.php');
define('ROUTER', CORE.'Router.php');
define('DATABASE', CORE.'Database.php');
define('MODEL', API.'models'.DS.'Model.php');
define('RESPONSE', API.'responses'.DS.'Response.php');
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
define('HOST', 'localhost');
define('DBNAME', 'phasil');
define('USERNAME', 'root');
define('PASSWORD', 'root');
define('TABLE_PREFIX', '');

// CORE
require_once HELPER;
require_once ROUTER;
require_once MODEL;
require_once RESPONSE;

// HEADERS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: ".implode(',', ALLOWED_METHODS));
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");