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

                Route::resource('/', 'ProductController'); //CRUD
                //Доп. - сменить категорию, добавить фото
                Route::get('/attribute/groups', 'AttributeController@groups')->name('attribute.groups');
                Route::delete('/attribute/group-destroy/{group}', 'AttributeController@group_destroy')->name('attribute.group-destroy');

                Route::post('/attribute/group-add', 'AttributeController@group_add')->name('attribute.group-add');
                Route::post('/attribute/group-rename/{group}', 'AttributeController@group_rename')->name('attribute.group-rename');
                Route::post('/attribute/variant-image/{variant}', 'AttributeController@variant_image')->name('attribute.variant-image');

                Route::post('/attribute/{group}/group-up', 'AttributeController@group_up')->name('attribute.group-up');
                Route::post('/attribute/{group}/group-down', 'AttributeController@group_down')->name('attribute.group-down');

                Route::post('/category/{category}/up', 'CategoryController@up')->name('category.up');
                Route::post('/category/{category}/down', 'CategoryController@down')->name('category.down');
                Route::get('/category/{category}/child', 'CategoryController@child')->name('category.child');

                Route::get('/tags', 'TagController@index')->name('tag.index');
                Route::post('/tag/create', 'TagController@create')->name('tag.create');
                Route::post('/tag/{tag}/rename', 'TagController@rename')->name('tag.rename');
                Route::delete('/tag/{tag}/destroy', 'TagController@destroy')->name('tag.destroy');


                Route::resource('brand', 'BrandController'); //CRUD
                Route::resource('category', 'CategoryController'); //CRUD
                Route::resource('attribute', 'AttributeController'); //CRUD

            }
        );


    }
);

