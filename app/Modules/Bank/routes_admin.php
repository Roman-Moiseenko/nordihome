<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'bank',
        'as' => 'bank.',
    ],
    function () {

        Route::post('/upload', 'BankController@upload')->name('upload');
        Route::post('/currency', 'BankController@currency')->name('currency');

    });
