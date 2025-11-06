<?php

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



