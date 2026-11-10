<?php

use App\Modules\Marketplace\Presentation\Http\Controllers\Web\AvitoController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/marketplace',
    'as' => 'admin.marketplace.',
],function () {
    //Маршруты тут

    Route::group([
        'prefix' => 'avito',
        'as' => 'avito.',
    ], function () {
        Route::get('/', [AvitoController::class, 'index'])->name('index');
    });
});
