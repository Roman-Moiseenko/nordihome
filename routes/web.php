<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();
Route::get('/profile', [\App\Http\Controllers\User\CabinetController::class, 'profile'])->name('user.profile');
Route::get('/cabinet', [\App\Http\Controllers\User\CabinetController::class, 'index'])->name('user.cabinet');

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

        //Route::resource('users', 'UsersController');
        Route::get('/users', 'UsersController@index')->name('users.index');
        Route::get('/users/{user}', 'UsersController@show')->name('users.show');
        Route::post('/users/{user}/verify', 'UsersController@verify')->name('users.verify');

        Route::resource('staff', 'StaffController'); //CRUD

        Route::get('/staff/{staff}/security', 'StaffController@security')->name('staff.security');
        Route::post('/staff/password/{staff}', 'StaffController@password')->name('staff.password');
        Route::post('/staff/activate/{staff}', 'StaffController@activate')->name('staff.activate');
        Route::post('/staff/photo/{staff}', 'StaffController@setPhoto')->name('staff.photo');

        //**** SHOP
        //Product
        Route::group(
            [
                'prefix' => 'product',
                'as' => 'product.',
                'namespace' => 'Product',
            ],
            function () {
                Route::get('/', 'ProductController@index')->name('index');
                //Route::get('/brands', 'BrandController@index')->name('brand.index');
                Route::resource('brand', 'BrandController'); //CRUD
                Route::resource('category', 'CategoryController'); //CRUD
                Route::post('/category/up/{category}', 'CategoryController@up')->name('category.up');
                Route::post('/category/down/{category}', 'CategoryController@down')->name('category.down');
                Route::get('/category/child/{category}', 'CategoryController@child')->name('category.child');

            }
        );


    }
);

