<?php


use App\Modules\Admin\Controllers\HomeController;
use App\Modules\Admin\Controllers\ShopSettingsController;
use App\Modules\Admin\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;


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
