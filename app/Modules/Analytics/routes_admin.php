<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'analytics',
        'as' => 'analytics.',
       // 'namespace' => 'Analytics',
    ],
    function () {
        Route::resource('cron', 'CronController')->only(['index', 'show']); //CRUD
        Route::resource('activity', 'ActivityController')->only(['index']); //CRUD
    }
);
