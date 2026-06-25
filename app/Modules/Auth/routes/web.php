<?php

// use Illuminate\Support\Facades\Route;

// Route::middleware([])->prefix('auth')->group(function () {

//     Route::get('/', function () {
//         return 'auth';
//     });

// });
use App\Modules\Auth\Presentation\Http\Controllers\Web\AuthController;
use App\Modules\Auth\Presentation\Http\Controllers\Web\ClientController;
use App\Modules\Auth\Presentation\Http\Controllers\Web\FreelanceController;
use App\Modules\Auth\Presentation\Http\Controllers\Web\RoleController;
use App\Modules\Auth\Presentation\Http\Controllers\Web\StaffController;
use Illuminate\Support\Facades\Route;

// Аутентификация сотрудников (Inertia)
//Route::get('/admin/login', [StaffLoginController::class, 'showLoginForm'])->name('admin.login');
//Route::post('/admin/login', [StaffLoginController::class, 'login']);
//Route::any('/admin/logout', [StaffLoginController::class, 'logout'])->name('admin.logout');

// Подтверждение email клиента
Route::get('/verify-email', [ClientController::class, 'verifyEmail'])->name('verify-email');

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
],function () {
    Route::post('/login', [AuthController::class, 'login']);

    //Без доступа
    //Аутентификация
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    ///Регистрация клиента восстановление пароля
    Route::group([
        'prefix' => 'client',
    ], function () {
        //TODO Сделать позже
        Route::post('/registration', [ClientController::class, 'registration']);
//        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
//        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    //С доступом
    Route::middleware('auth')->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);
        //Клиенты Client
        // Клиент может управлять своим профилем
        Route::post('/client/credentials', [ClientController::class, 'credentials']); //смена регистр.данных
        Route::get('/client/profile', [ClientController::class, 'profile']);
        Route::put('/client/profile', [ClientController::class, 'updateProfile']);
        // Админские маршруты для управления клиентами
        Route::middleware(['role:admin|staff'])->group(function () {
            Route::apiResource('client', ClientController::class);
            Route::post('/client/{id}/register', [ClientController::class, 'register']);
        });

        // маршруты для управления сотрудниками
        Route::middleware(['role:admin|staff'])->group(function () {
            //Route::get('/user', [AuthController::class, 'profile']);
            //Сотрудники Staff
            Route::get('staff/positions', [StaffController::class, 'positions'])->name('staff.positions');
            Route::get('permission/grouped', [RoleController::class, 'permissions'])->name('role.permissions');
            Route::get('roles', [RoleController::class, 'roles'])->name('role.roles');
            Route::Resource('staff', StaffController::class);
            Route::post('/staff/{id}/user', [StaffController::class, 'user'])->name('staff.user');

            //Внештатные сотрудники Freelance
            Route::Resource('freelance', FreelanceController::class);
            Route::post('/freelance/{id}/user', [FreelanceController::class, 'user']);

            //Управление ролями
            Route::Resource('role', RoleController::class)->except(['create', 'edit']);
        });
    });
});
