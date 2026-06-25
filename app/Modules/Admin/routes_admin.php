<?php


use App\Modules\Admin\Controllers\HomeController;
use App\Modules\Admin\Controllers\ShopSettingsController;
use App\Modules\Admin\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'worker',
    'as' => 'worker.',
], function () {
    Route::post('/toggle/{worker}', [WorkerController::class, 'toggle'])->name('toggle');
    Route::post('/update/{worker}', [WorkerController::class, 'update'])->name('update');
});


Route::resource('worker', 'WorkerController')->except(['create', 'edit', 'update']); //CRUD

//Настройки
Route::group(
    [
        'prefix' => 'settings',
        'as' => 'settings.',
    ],
    function () {
        Route::get('/shop', [ShopSettingsController::class, 'index'])->name('shop');
        Route::post('/shop', [ShopSettingsController::class, 'update']);
    }
);

Route::get('/', [HomeController::class, 'index'])->name('home');
