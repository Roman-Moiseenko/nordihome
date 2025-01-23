<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'product',
        'as' => 'product.',
        //'namespace' => 'Product',
    ],
    function () {

        Route::post('/action', 'ProductController@action')->name('action');
        //Атрибуты
        Route::group([
            'prefix' => 'attribute',
            'as' => 'attribute.',
        ], function () {
            //Доп. - сменить категорию, добавить фото
            Route::get('/groups', 'AttributeController@groups')->name('groups');
            Route::delete('/group-destroy/{group}', 'AttributeController@group_destroy')->name('group-destroy');

            Route::post('/group-add', 'AttributeController@group_add')->name('group-add');
            Route::post('/group-rename/{group}', 'AttributeController@group_rename')->name('group-rename');
            Route::post('/variant-image/{variant}', 'AttributeController@variant_image')->name('variant-image');

            Route::post('/group-up/{group}', 'AttributeController@group_up')->name('group-up');
            Route::post('/set-info/{attribute}', 'AttributeController@set_info')->name('set-info');

            Route::post('/group-down/{group}', 'AttributeController@group_down')->name('group-down');
        });
        //BRAND
        Route::group([
            'prefix' => 'brand',
            'as' => 'brand.',
        ], function () {
            Route::post('/list', 'BrandController@list')->name('list');
            Route::post('/set-info/{brand}', 'BrandController@set_info')->name('set-info');

        });
        //CATEGORY
        Route::group([
            'prefix' => 'category',
            'as' => 'category.',
        ], function () {
            Route::post('/up/{category}', 'CategoryController@up')->name('up');
            Route::post('/down/{category}', 'CategoryController@down')->name('down');
            Route::get('/child/{category}', 'CategoryController@child')->name('child');
            Route::post('/list', 'CategoryController@list')->name('list');
            Route::post('/set-info/{category}', 'CategoryController@set_info')->name('set-info');
        });
        //TAG
        Route::group([
            'prefix' => 'tag',
            'as' => 'tag.',
        ], function () {
            Route::get('/', 'TagController@index')->name('index');
            Route::post('/store', 'TagController@store')->name('store');
            Route::post('/rename/{tag}', 'TagController@rename')->name('rename');
            Route::delete('/destroy/{tag}', 'TagController@destroy')->name('destroy');
        });
        //EQUIVALENT
        Route::group([
            'prefix' => 'equivalent',
            'as' => 'equivalent.',
        ], function () {
            Route::post('/rename/{equivalent}', 'EquivalentController@rename')->name('rename');
            Route::post('/add-product/{equivalent}', 'EquivalentController@add_product')->name('add-product');
            Route::delete('/del-product/{equivalent}', 'EquivalentController@del_product')->name('del-product');
            Route::post('/json-products/{equivalent}', 'EquivalentController@json_products')->name('json-products');
            Route::post('/search/{equivalent}', 'EquivalentController@search')->name('search');
        });
        //Группа товаров
        Route::group([
            'prefix' => 'group',
            'as' => 'group.',
        ], function () {
            Route::post('/add-products/{group}', 'GroupController@add_products')->name('add-products');
            Route::post('/add-product/{group}', 'GroupController@add_product')->name('add-product');
            Route::post('/set-info/{group}', 'GroupController@set_info')->name('set-info');
            Route::delete('/del-product/{group}', 'GroupController@del_product')->name('del-product');
        });
        //Серия товаров
        Route::group([
            'prefix' => 'series',
            'as' => 'series.',
        ], function () {
            Route::post('/add-product/{series}', 'SeriesController@add_product')->name('add-product');
            Route::post('/add-products/{series}', 'SeriesController@add_products')->name('add-products');
            Route::delete('/del-product/{series}', 'SeriesController@del_product')->name('del-product');
        });
        //Приоритеты
        Route::group([
            'prefix' => 'priority',
            'as' => 'priority.',
        ], function () {
            Route::get('/', 'PriorityController@index')->name('index');
            Route::post('/add-product', 'PriorityController@add_product')->name('add-product');
            Route::post('/add-products', 'PriorityController@add_products')->name('add-products');
            Route::delete('/del-product/{product}', 'PriorityController@del_product')->name('del-product');
        });
        //SIZE
        /*
        Route::group([
            'prefix' => 'size',
            'as' => 'size.',
        ], function () {
            Route::post('/{category}/add-size', 'SizeController@add_size')->name('add-size');
            Route::post('/{size}/del-size', 'SizeController@del_size')->name('del-size');
            Route::post('/{size}/set-size', 'SizeController@set_size')->name('set-size');
            Route::post('/', 'SizeController@store')->name('store');
            Route::get('/{category}', 'SizeController@show')->name('show');
            Route::delete('/{category}', 'SizeController@destroy')->name('destroy');

            Route::get('/', 'SizeController@index')->name('index');
        });
        */
        //MODIFICATION
        Route::group([
            'prefix' => 'modification',
            'as' => 'modification.',
        ], function () {
            Route::post('/set-modifications/{modification}', 'ModificationController@set_modifications')->name('set-modifications');
            Route::post('/search', 'ModificationController@search')->name('search');
            Route::post('/rename/{modification}', 'ModificationController@rename')->name('rename');
            Route::post('/add-product/{modification}', 'ModificationController@add_product')->name('add-product');
            Route::delete('/del-product/{modification}', 'ModificationController@del_product')->name('del-product');
        });
        //PARSER
        Route::group([
            'prefix' => 'parser',
            'as' => 'parser.',
        ], function () {
            Route::get('/', 'ParserController@index')->name('index');
            Route::get('/show/{parser}', 'ParserController@show')->name('show');
            Route::post('/block/{parser}', 'ParserController@block')->name('block');
            Route::post('/fragile/{parser}', 'ParserController@fragile')->name('fragile');
            Route::post('/sanctioned/{parser}', 'ParserController@sanctioned')->name('sanctioned');
        });


        //resource
        Route::resource('brand', 'BrandController'); //CRUD
        Route::resource('category', 'CategoryController'); //CRUD
        Route::resource('attribute', 'AttributeController'); //CRUD
        Route::resource('equivalent', 'EquivalentController'); //CRUD
        Route::resource('group', 'GroupController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('modification', 'ModificationController'); //CRUD
        Route::resource('series', 'SeriesController')->except(['create', 'edit']); //CRUD
      //  Route::resource('size', 'SizeController')->except(['create', 'edit']); //CRUD

        //PRODUCT
        Route::group([
            'prefix' => 'image',
            'as' => 'image.',
        ], function () {
            Route::post('/add/{product}', 'ProductController@add_image')->name('add');
            Route::post('/get/{product}', 'ProductController@get_images')->name('get');
            Route::delete('/del/{product}', 'ProductController@del_image')->name('del');
            Route::post('/up/{product}', 'ProductController@up_image')->name('up');
            Route::post('/down/{product}', 'ProductController@down_image')->name('down');
            Route::post('/set/{product}', 'ProductController@set_image')->name('set');
            Route::post('/move/{product}', 'ProductController@move_image')->name('move');
        });
        Route::group([
            'prefix' => 'edit',
            'as' => 'edit.'
        ], function () {
            Route::post('/common/{product}', 'ProductController@edit_common')->name('common');
            Route::post('/description/{product}', 'ProductController@edit_description')->name('description');
            Route::post('/dimensions/{product}', 'ProductController@edit_dimensions')->name('dimensions');
            Route::post('/video/{product}', 'ProductController@edit_video')->name('video');
            Route::post('/attribute/{product}', 'ProductController@edit_attribute')->name('attribute');
            Route::post('/management/{product}', 'ProductController@edit_management')->name('management');
            Route::post('/equivalent/{product}', 'ProductController@edit_equivalent')->name('equivalent');
            Route::post('/related/{product}', 'ProductController@edit_related')->name('related');
            Route::post('/bonus/{product}', 'ProductController@edit_bonus')->name('bonus');
            Route::post('/composite/{product}', 'ProductController@edit_composite')->name('composite');
        });

        Route::post('/rename/{product}', 'ProductController@rename')->name('rename');
        Route::post('/search', 'ProductController@search')->name('search');
        Route::post('/search-add', 'ProductController@search_add')->name('search-add');
        Route::post('/search_bonus', 'ProductController@search_bonus')->name('search-bonus');
        Route::post('/attr-modification/{product}', 'ProductController@attr_modification')->name('attr-modification');
        Route::post('/toggle/{product}', 'ProductController@toggle')->name('toggle');
        Route::post('/sale/{product}', 'ProductController@sale')->name('sale');
        Route::post('/restore/{id}', 'ProductController@restore')->name('restore');
        Route::delete('/full-delete/{id}', 'ProductController@full_delete')->name('full-delete');
        Route::post('/fast-create', 'ProductController@fast_create')->name('fast-create');

    }
);

Route::resource('product', 'ProductController');
