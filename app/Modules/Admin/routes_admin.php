<?php


use App\Modules\Admin\Controllers\HomeController;
use App\Modules\Admin\Controllers\ShopSettingsController;
use App\Modules\Admin\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

// Главная страница админки (Inertia Admin/Home)
Route::get('/', [HomeController::class, 'index'])->name('home');
