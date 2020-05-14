<?php

require 'config/Core.php';

use Api\Route;

// ENDPOINTS SETUP
Route::Create('POST', '/register', 'users/register');
Route::Create('PUT', '/registerPush', 'users/registerDevice');
Route::Create('POST', '/login', 'users/login');
Route::Create('POST', '/users/listAll', 'users/getByFilter');
Route::Create('GET', '/home', 'dom/info');

// RENDER RESPONSE
echo Route::Read(METHOD, REQUEST, BODY);

