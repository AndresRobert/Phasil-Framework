<?php

include 'config/Core.php';

use Api\Route;

// ENDPOINTS SETUP
Route::Clear();
Route::Create('GET', '/home', 'dom/info');
Route::Create('POST', '/users/listAll', 'dom/user_test');

// RENDER RESPONSE
echo Route::Read(METHOD, REQUEST, BODY);

