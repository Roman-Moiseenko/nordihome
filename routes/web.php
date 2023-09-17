<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();
Route::get('/cabinet', [\App\Http\Controllers\Cabinet\HomeController::class, 'index'])->name('cabinet');

Route::get('/verify/{token}', [\App\Http\Controllers\Auth\RegisterController::class, 'verify'])->name('register.verify');

Route::get('/admin/login',[\App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login',[\App\Http\Controllers\Auth\LoginController::class, 'adminLogin']);//->name('admin.login');

Route::post('/file-upload', [\App\Http\Controllers\Admin\StaffController::class, 'test']);


Route::group(
    [
        'prefix' => 'admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\Admin',
        'middleware' => ['auth:admin'],
    ],
    function () {
        Route::get('/', 'HomeController@index')->name('home');

        Route::resource('users', 'UsersController'); //CRUD
        Route::resource('staff', 'StaffController'); //CRUD

        Route::get('/staff/{staff}/security', 'StaffController@security')->name('staff.security');
        Route::post('/staff/password/{staff}', 'StaffController@password')->name('staff.password');
        Route::post('/staff/activate/{staff}', 'StaffController@activate')->name('staff.activate');
        Route::post('/staff/photo/{staff}', 'StaffController@setphoto')->name('staff.photo');

        Route::post('/users/{user}/verify', 'UsersController@verify')->name('users.verify');
    }
);

