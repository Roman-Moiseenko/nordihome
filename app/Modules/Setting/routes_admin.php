<?php

use App\Modules\Setting\Controllers\SettingCacheController;
use App\Modules\Setting\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SettingController::class, 'index'])->name('setting.index');

Route::get('/setting/parser', [SettingController::class, 'parser'])->name('setting.parser');

Route::get('/setting/common', [SettingController::class, 'common'])->name('setting.common');
Route::get('/setting/coupon', [SettingController::class, 'coupon'])->name('setting.coupon');
Route::get('/setting/web', [SettingController::class, 'web'])->name('setting.web');
Route::get('/setting/mail', [SettingController::class, 'mail'])->name('setting.mail');
Route::get('/setting/notification', [SettingController::class, 'notification'])->name('setting.notification');
Route::get('/setting/image', [SettingController::class, 'image'])->name('setting.image');

Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');


Route::group([
    'prefix' => 'setting/cache',
    'as' => 'setting.cache.'
], function () {
    Route::get('/', [SettingCacheController::class, 'index'])->name('index');

    Route::post('/clear-all', [SettingCacheController::class, 'clearAll'])->name('clear-all');
    Route::post('/clearImage', [SettingCacheController::class, 'clearImage'])->name('clear-image');

    Route::post('/recache', [SettingCacheController::class, 'recache'])->name('recache');
});



