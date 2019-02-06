<?php

use Illuminate\Http\Request;
use Illuminate\Http\Controllers;

        //login & register
Route::POST('login', 'AuthController@login');

Route::POST('logout', 'AuthController@logout');

Route::post('register', 'AuthController@register');
