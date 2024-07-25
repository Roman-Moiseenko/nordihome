<?php


use Illuminate\Support\Facades\Route;


Route::any('/api/telegram', [\App\Http\Controllers\Api\TelegramController::class, 'get'])->name('api.telegram');

//Admin

Route::get('/admin/login', [\App\Modules\Admin\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [\App\Modules\Admin\Controllers\Auth\LoginController::class, 'login']);
Route::any('/admin/logout', [\App\Modules\Admin\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');




