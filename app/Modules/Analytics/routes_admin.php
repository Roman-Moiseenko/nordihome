<?php

use App\Modules\Analytics\Controllers\ActivityController;
use App\Modules\Analytics\Controllers\CronController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => 'role:admin|staff',
        'prefix' => 'analytics',
        'as' => 'analytics.',
       // 'namespace' => 'Analytics',
    ],
    function () {
        Route::resource('cron', 'CronController')->only(['index', 'show']); //CRUD
        Route::resource('activity', 'ActivityController')->only(['index']); //CRUD
    }
);
