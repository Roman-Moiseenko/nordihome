<?php

use Illuminate\Support\Facades\Route;

if (config('shop.theme') != 'nbrussia') return;

Route::group(
    [
        'prefix' => 'nbrussia',
        'as' => 'nbrussia.',
    ], function () {

    Route::get('/', 'TestController@index')->name('index');
    Route::post('/test', 'TestController@test')->name('test');


});
