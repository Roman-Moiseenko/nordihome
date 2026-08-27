<?php


use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => 'role:admin|staff',
        'prefix' => 'discount',
        'as' => 'discount.',
    ],
    function () {
    }
);
