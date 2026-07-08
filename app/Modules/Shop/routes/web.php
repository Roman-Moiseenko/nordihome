<?php

use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin',
    'as' => 'admin.',
],function () {
    //Маршруты тут
});
