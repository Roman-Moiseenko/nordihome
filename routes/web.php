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

                //Доп. - сменить категорию, добавить фото
                Route::get('/attribute/groups', 'AttributeController@groups')->name('attribute.groups');
                Route::delete('/attribute/group-destroy/{group}', 'AttributeController@group_destroy')->name('attribute.group-destroy');

                Route::post('/attribute/group-add', 'AttributeController@group_add')->name('attribute.group-add');
                Route::post('/attribute/group-rename/{group}', 'AttributeController@group_rename')->name('attribute.group-rename');
                Route::post('/attribute/variant-image/{variant}', 'AttributeController@variant_image')->name('attribute.variant-image');
                //Route::post('/attribute/get_by_categories', 'AttributeController@get_by_categories')->name('attribute.get-by-categories');

                Route::post('/attribute/{group}/group-up', 'AttributeController@group_up')->name('attribute.group-up');
                Route::post('/attribute/{group}/group-down', 'AttributeController@group_down')->name('attribute.group-down');

                Route::post('/category/{category}/up', 'CategoryController@up')->name('category.up');
                Route::post('/category/{category}/down', 'CategoryController@down')->name('category.down');
                Route::get('/category/{category}/child', 'CategoryController@child')->name('category.child');
                Route::post('/category/json_attributes', 'CategoryController@json_attributes')->name('attribute.json-attributes');


                Route::get('/tags', 'TagController@index')->name('tag.index');
                Route::post('/tag/create', 'TagController@create')->name('tag.create');
                Route::post('/tag/{tag}/rename', 'TagController@rename')->name('tag.rename');
                Route::delete('/tag/{tag}/destroy', 'TagController@destroy')->name('tag.destroy');

                Route::get('/equivalent', 'EquivalentController@index')->name('equivalent.index');
                Route::get('/equivalent/show', 'EquivalentController@show')->name('equivalent.show');
                Route::post('/equivalent/store', 'EquivalentController@create')->name('equivalent.store');
                Route::post('/equivalent/{equivalent}/rename', 'EquivalentController@rename')->name('equivalent.rename');
                Route::post('/equivalent/{equivalent}/add-product', 'EquivalentController@add_product')->name('equivalent.add-product');
                Route::delete('/equivalent/{equivalent}/del-product', 'EquivalentController@del_product')->name('equivalent.del-product');
                Route::delete('/equivalent/{equivalent}/destroy', 'EquivalentController@destroy')->name('equivalent.destroy');

                Route::resource('brand', 'BrandController'); //CRUD
                Route::resource('category', 'CategoryController'); //CRUD
                Route::resource('attribute', 'AttributeController'); //CRUD
                Route::resource('equivalent', 'EquivalentController'); //CRUD

            }
        );
        //AJAX Product-Image
        Route::post('product/{product}/file-upload', 'Product\ProductController@file_upload')->name('product.file-upload');
        Route::post('product/{product}/get-images', 'Product\ProductController@get_images')->name('product.get-images');
        Route::post('product/{product}/del-image', 'Product\ProductController@del_image')->name('product.del-image');
        Route::post('product/{product}/up-image', 'Product\ProductController@up_image')->name('product.up-image');
        Route::post('product/{product}/down-image', 'Product\ProductController@down_image')->name('product.down-image');
        Route::post('product/{product}/alt-image', 'Product\ProductController@alt_image')->name('product.alt-image');

        Route::resource('product', 'Product\ProductController'); //CRUD

    }
);

