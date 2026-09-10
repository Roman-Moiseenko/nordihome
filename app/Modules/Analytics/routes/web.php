<?php

use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/analytics',
    'as' => 'admin.analytics.',
],function () {
    //Маршруты тут
});
