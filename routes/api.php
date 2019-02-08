<?php

use Illuminate\Http\Request;

        //login & register
Route::POST('login', 'ِِِAuthApi/AuthController@login');

Route::POST('logout', 'ِِِAuthApi/AuthController@logout');

Route::Post('register', 'AuthApi\RegisterController@register');

Route::get('register/verify/{confirmationCode}' ,  'AuthApi\ConfirmController@confirm');



