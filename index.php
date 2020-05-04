<?php

include 'core/Config.php';

use Core\Routes\Route;

// ENDPOINTS SETUP
Route::Create('GET', '/', 'dom/info');
Route::Create('POST', '/users/listAll', 'dom/user_test');

// RENDER RESPONSE
echo Route::Read(METHOD, REQUEST, BODY);
