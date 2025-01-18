<?php

use Illuminate\Support\Facades\Route;

if (config('shop.theme') != 'nordihome') return;

Route::group(
    [
        'prefix' => 'nordihome',
        'as' => 'nordihome.',
    ], function () {

    Route::get('/', 'FunctionController@index')->name('index');
    Route::post('/parser-dimensions', 'FunctionController@parser_dimensions')->name('parser-dimensions');

});
