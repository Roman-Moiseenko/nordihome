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
        ], function() {
            //Доп. - сменить категорию, добавить фото
            Route::get('/groups', 'AttributeController@groups')->name('groups');
            Route::delete('/group-destroy/{group}', 'AttributeController@group_destroy')->name('group-destroy');

            Route::post('/group-add', 'AttributeController@group_add')->name('group-add');
            Route::post('/group-rename/{group}', 'AttributeController@group_rename')->name('group-rename');
            Route::post('/variant-image/{variant}', 'AttributeController@variant_image')->name('variant-image');
            //Route::post('/get_by_categories', 'AttributeController@get_by_categories')->name('get-by-categories');

            Route::post('/group-up/{group}', 'AttributeController@group_up')->name('group-up');
            Route::post('/group-down/{group}', 'AttributeController@group_down')->name('group-down');
        });


        Route::post('/category/{category}/up', 'CategoryController@up')->name('category.up');
        Route::post('/category/{category}/down', 'CategoryController@down')->name('category.down');
        Route::get('/category/{category}/child', 'CategoryController@child')->name('category.child');
        //Route::post('/category/json_attributes', 'CategoryController@json_attributes')->name('attribute.json-attributes');

        Route::get('/tags', 'TagController@index')->name('tag.index');
        Route::post('/tag/create', 'TagController@create')->name('tag.create');
        Route::post('/tag/{tag}/rename', 'TagController@rename')->name('tag.rename');
        Route::delete('/tag/{tag}/destroy', 'TagController@destroy')->name('tag.destroy');

        // Route::get('/equivalent', 'EquivalentController@index')->name('equivalent.index');
        //Route::get('/equivalent/show', 'EquivalentController@show')->name('equivalent.show');
        //Route::post('/equivalent/store', 'EquivalentController@create')->name('equivalent.store');
        Route::post('/equivalent/{equivalent}/rename', 'EquivalentController@rename')->name('equivalent.rename');
        Route::post('/equivalent/{equivalent}/add-product', 'EquivalentController@add_product')->name('equivalent.add-product');
        Route::delete('/equivalent/{equivalent}/del-product/{product}', 'EquivalentController@del_product')->name('equivalent.del-product');
        //Route::delete('/equivalent/{equivalent}/destroy', 'EquivalentController@destroy')->name('equivalent.destroy');
        Route::post('/equivalent/{equivalent}/json-products', 'EquivalentController@json_products')->name('equivalent.json-products');

        //Группа товаров
        Route::group([
            'prefix' => 'group',
            'as' => 'group.',
        ], function() {
            Route::post('/add-products/{group}', 'GroupController@add_products')->name('add-products');
            Route::post('/add-product/{group}', 'GroupController@add_product')->name('add-product');
            Route::delete('/del-product/{group}', 'GroupController@del_product')->name('del-product');
        });
        //Серия товаров
        Route::group([
            'prefix' => 'series',
            'as' => 'series.',
        ], function() {
            Route::post('/add-product/{series}', 'SeriesController@add_product')->name('add-product');
            Route::post('/add-products/{series}', 'SeriesController@add_products')->name('add-products');
            Route::delete('/del-product/{series}', 'SeriesController@del_product')->name('del-product');
        });

        //Приоритеты
        Route::group([
            'prefix' => 'priority',
            'as' => 'priority.',
        ], function() {
            Route::get('/', 'PriorityController@index')->name('index');
            Route::post('/add-product', 'PriorityController@add_product')->name('add-product');
            Route::post('/add-products', 'PriorityController@add_products')->name('add-products');
            Route::delete('/del-product/{product}', 'PriorityController@del_product')->name('del-product');
        });



        Route::post('/modification/{modification}/set-modifications', 'ModificationController@set_modifications')->name('modification.set-modifications');
        Route::post('/modification/search', 'ModificationController@search')->name('modification.search');
        Route::post('/modification/{modification}/add-product', 'ModificationController@add_product')->name('modification.add-product');
        Route::delete('/modification/{modification}/del-product', 'ModificationController@del_product')->name('modification.del-product');

        Route::get('/parser', 'ParserController@index')->name('parser.index');
        Route::get('/parser/{parser}/show', 'ParserController@show')->name('parser.show');
        Route::post('/parser/{parser}/block', 'ParserController@block')->name('parser.block');
        Route::post('/parser/{parser}/fragile', 'ParserController@fragile')->name('parser.fragile');
        Route::post('/parser/{parser}/sanctioned', 'ParserController@sanctioned')->name('parser.sanctioned');

        Route::resource('brand', 'BrandController'); //CRUD
        Route::resource('category', 'CategoryController'); //CRUD
        Route::resource('attribute', 'AttributeController'); //CRUD
        Route::resource('equivalent', 'EquivalentController'); //CRUD
        Route::resource('group', 'GroupController'); //CRUD
        Route::resource('modification', 'ModificationController'); //CRUD
        Route::resource('series', 'SeriesController')->except(['create', 'edit']); //CRUD


        Route::post('/{product}/file-upload', 'ProductController@file_upload')->name('file-upload');
        Route::post('/{product}/get-images', 'ProductController@get_images')->name('get-images');
        Route::post('/{product}/del-image', 'ProductController@del_image')->name('del-image');
        Route::post('/{product}/up-image', 'ProductController@up_image')->name('up-image');
        Route::post('/{product}/down-image', 'ProductController@down_image')->name('down-image');
        Route::post('/{product}/alt-image', 'ProductController@alt_image')->name('alt-image');
        Route::post('/search', 'ProductController@search')->name('search');
        Route::post('/search-add', 'ProductController@search_add')->name('search-add');
        Route::post('/search_bonus', 'ProductController@search_bonus')->name('search-bonus');
        Route::post('/{product}/attr-modification', 'ProductController@attr_modification')->name('attr-modification');
        Route::post('/toggle/{product}', 'ProductController@toggle')->name('toggle');
    }
);

Route::resource('product', 'ProductController');
