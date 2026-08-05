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
use App\Modules\User\Controllers\Auth\ForgotPasswordController;
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
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');


    //С доступом
    Route::middleware(['auth', 'role:admin|staff'])->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);

        // Админские маршруты для управления клиентами
        Route::apiResource('client', ClientController::class);
        Route::post('/client/{id}/register', [ClientController::class, 'register']);

        // Маршруты для управления сотрудниками
        Route::get('staff/positions', [StaffController::class, 'positions'])->name('staff.positions');
        Route::get('staff/groups', [StaffController::class, 'groups'])->name('staff.groups');
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
//Клиентская часть
//без доступа
Route::group(
    [
        'middleware' => ['user_cookie_id'],
    ],
    function () {


        //Без доступа
        //Аутентификация или регистрация
        Route::any('/login-client', [AuthController::class, 'loginClient'])->name('login');
        //TODO Переделать
        Route::any('/password/request', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::get('/register/verify', [AuthController::class, 'verify'])->name('register.verify');
        ///Регистрация клиента восстановление пароля
        Route::group([
            'prefix' => 'client',
        ], function () {
            //TODO Сделать позже
           // Route::post('/registration', [ClientController::class, 'registration']);
//        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
//        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        });



    }
);

//с доступом
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::any('/logout', [AuthController::class, 'logoutClient'])->name('logout');


    //Клиенты Client
    // Клиент может управлять своим профилем
    Route::post('/client/credentials', [ClientController::class, 'credentials']); //смена регистр.данных
    Route::get('/client/profile', [ClientController::class, 'profile']);
    Route::put('/client/profile', [ClientController::class, 'updateProfile'])->name('client.update-profile');

});
