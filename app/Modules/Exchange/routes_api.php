<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'exchange',
    'as'=> 'exchange.'
], function () {
    Route::any('/1c', 'Exchange1CController@web_hook')->name('1c');
    Route::any('/get-products', 'Exchange1CController@products')->name('get-products');
});



