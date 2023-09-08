<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();
Route::get('/cabinet', [\App\Http\Controllers\Cabinet\HomeController::class, 'index'])->name('cabinet');


Route::get('/verify/{token}', [\App\Http\Controllers\Auth\RegisterController::class, 'verify'])->name('register.verify');


/*
    Route::middleware(['auth'])->group(function () {

        Route::get('/Admin', 'App\Http\Controllers\Admin\HomeController@index')->name('admin.home');
        Route::get('/Admin/users', 'App\Http\Controllers\Admin\UsersController@show')->name('admin.users.show');
        Route::get('/Admin/users/create', 'App\Http\Controllers\Admin\UsersController@create')->name('admin.users.create');
        Route::get('/Admin/users', 'App\Http\Controllers\Admin\UsersController@store')->name('admin.users.store');
        Route::get('/Admin/users', 'App\Http\Controllers\Admin\UsersController@edit')->name('admin.users.edit');
        Route::get('/Admin/users', 'App\Http\Controllers\Admin\UsersController@update')->name('admin.users.update');
        Route::get('/Admin/users', 'App\Http\Controllers\Admin\UsersController@destroy')->name('admin.users.destroy');
        Route::get('/Admin/users', 'App\Http\Controllers\Admin\UsersController@index')->name('admin.users.index');
    });

*/
/*
Или так - >
*/

Route::group(
    [
        'prefix' => 'Admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\Admin',
        'middleware' => ['auth'],
    ],
    function () {
        Route::get('/', 'HomeController@index')->name('home');
        Route::resource('users', 'UsersController'); //CRUD

        Route::post('/users/{user}/verify', 'UsersController@verify')->name('users.verify');
    }
);
