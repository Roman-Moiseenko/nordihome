<?php


use App\Modules\Admin\Controllers\HomeController;
use App\Modules\Admin\Controllers\ShopSettingsController;
use App\Modules\Admin\Controllers\StaffController;
use App\Modules\Admin\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'staff',
    'as' => 'staff.',
], function () {
    Route::get('/notification', [StaffController::class, 'notification'])->name('notification');
    Route::post('/notification-read/{notification}', [StaffController::class, 'notification_read'])->name('notification-read');

    Route::get('/security/{staff}', [StaffController::class, 'security'])->name('security');
    Route::post('/password/{staff}', [StaffController::class, 'password'])->name('password');
    Route::post('/activate/{staff}', [StaffController::class, 'activate'])->name('activate');
    //Route::post('/photo/{staff}', [StaffController::class, 'setPhoto'])->name('photo');
    Route::post('/responsibility/{staff}', [StaffController::class, 'responsibility'])->name('responsibility');
});
Route::group([
    'prefix' => 'worker',
    'as' => 'worker.',
], function () {
    Route::post('/toggle/{worker}', [WorkerController::class, 'toggle'])->name('toggle');
    Route::post('/update/{worker}', [WorkerController::class, 'update'])->name('update');
});

Route::resource('staff', 'StaffController',); //CRUD
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
