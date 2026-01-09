<?php

use App\Modules\Product\Controllers\AttributeController;
use App\Modules\Product\Controllers\BrandController;
use App\Modules\Product\Controllers\CategoryController;
use App\Modules\Product\Controllers\EquivalentController;
use App\Modules\Product\Controllers\GroupController;
use App\Modules\Product\Controllers\ModificationController;
use App\Modules\Product\Controllers\ParserController;
use App\Modules\Product\Controllers\PriorityController;
use App\Modules\Product\Controllers\ProductController;
use App\Modules\Product\Controllers\ReducedController;
use App\Modules\Product\Controllers\SeriesController;
use App\Modules\Product\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'product',
        'as' => 'product.',
        //'namespace' => 'Product',
    ],
    function () {

        Route::post('/action', [ProductController::class, 'action'])->name('action');
        Route::post('/upload', [ProductController::class, 'upload'])->name('upload');
        Route::post('/find-parser', [ProductController::class, 'find_parser'])->name('find-parser');
        //Атрибуты
        Route::group([
            'prefix' => 'attribute',
            'as' => 'attribute.',
        ], function () {
            //Доп. - сменить категорию, добавить фото
            Route::get('/groups', [AttributeController::class, 'groups'])->name('groups');
            Route::delete('/group-destroy/{group}', [AttributeController::class, 'group_destroy'])->name('group-destroy');

            Route::post('/group-add', [AttributeController::class, 'group_add'])->name('group-add');
            Route::post('/group-rename/{group}', [AttributeController::class, 'group_rename'])->name('group-rename');
            //Route::post('/variant-image/{variant}', [AttributeController::class, 'variant_image'])->name('variant-image');

            Route::post('/group-up/{group}', [AttributeController::class, 'group_up'])->name('group-up');
            Route::post('/set-info/{attribute}', [AttributeController::class, 'set_info'])->name('set-info');

            Route::post('/group-down/{group}', [AttributeController::class, 'group_down'])->name('group-down');
        });
        //BRAND
        Route::group([
            'prefix' => 'brand',
            'as' => 'brand.',
        ], function () {
            Route::post('/list', [BrandController::class, 'list'])->name('list');
            Route::post('/set-info/{brand}', [BrandController::class, 'set_info'])->name('set-info');

        });
        //CATEGORY
        Route::group([
            'prefix' => 'category',
            'as' => 'category.',
        ], function () {
            Route::post('/up/{category}', [CategoryController::class, 'up'])->name('up');
            Route::post('/down/{category}', [CategoryController::class, 'down'])->name('down');
            Route::get('/child/{category}', [CategoryController::class, 'child'])->name('child');
            Route::post('/list', [CategoryController::class, 'list'])->name('list');
            Route::post('/set-info/{category}', [CategoryController::class, 'set_info'])->name('set-info');
        });
        //TAG
        Route::group([
            'prefix' => 'tag',
            'as' => 'tag.',
        ], function () {
            Route::get('/', [TagController::class, 'index'])->name('index');
            Route::post('/store', [TagController::class, 'store'])->name('store');
            Route::post('/rename/{tag}', [TagController::class, 'rename'])->name('rename');
            Route::delete('/destroy/{tag}', [TagController::class, 'destroy'])->name('destroy');
        });
        //EQUIVALENT
        Route::group([
            'prefix' => 'equivalent',
            'as' => 'equivalent.',
        ], function () {
            Route::post('/rename/{equivalent}', [EquivalentController::class, 'rename'])->name('rename');
            Route::post('/add-product/{equivalent}', [EquivalentController::class, 'add_product'])->name('add-product');
            Route::delete('/del-product/{equivalent}', [EquivalentController::class, 'del_product'])->name('del-product');
            Route::post('/json-products/{equivalent}', [EquivalentController::class, 'json_products'])->name('json-products');
            Route::post('/search/{equivalent}', [EquivalentController::class, 'search'])->name('search');
        });
        //Группа товаров
        Route::group([
            'prefix' => 'group',
            'as' => 'group.',
        ], function () {
            Route::post('/add-products/{group}', [GroupController::class, 'add_products'])->name('add-products');
            Route::post('/add-product/{group}', [GroupController::class, 'add_product'])->name('add-product');
            Route::post('/set-info/{group}', [GroupController::class, 'set_info'])->name('set-info');
            Route::delete('/del-product/{group}', [GroupController::class, 'del_product'])->name('del-product');
            Route::post('/search/{group}', [GroupController::class, 'search'])->name('search');
        });
        //Серия товаров
        Route::group([
            'prefix' => 'series',
            'as' => 'series.',
        ], function () {
            Route::post('/add-product/{series}', [SeriesController::class, 'add_product'])->name('add-product');
            Route::post('/add-products/{series}', [SeriesController::class, 'add_products'])->name('add-products');
            Route::delete('/del-product/{series}', [SeriesController::class, 'del_product'])->name('del-product');
        });
        //Приоритеты
        Route::group([
            'prefix' => 'priority',
            'as' => 'priority.',
        ], function () {
            Route::get('/', [PriorityController::class, 'index'])->name('index');
            Route::post('/add-product', [PriorityController::class, 'add_product'])->name('add-product');
            Route::post('/add-products', [PriorityController::class, 'add_products'])->name('add-products');
            Route::delete('/del-product/{product}', [PriorityController::class, 'del_product'])->name('del-product');
        });
        //Снижение цен
        Route::group([
            'prefix' => 'reduced',
            'as' => 'reduced.',
        ], function () {
            Route::get('/', [ReducedController::class, 'index'])->name('index');
            Route::post('/add-product', [ReducedController::class, 'add_product'])->name('add-product');
            Route::post('/add-products', [ReducedController::class, 'add_products'])->name('add-products');
            Route::delete('/del-product/{product}', [ReducedController::class, 'del_product'])->name('del-product');
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
            //Route::post('/set-modifications/{modification}', [ModificationController::class, 'set_modifications'])->name('set-modifications');
            Route::post('/set-base/{modification}', [ModificationController::class, 'set_base'])->name('set-base');
            Route::post('/search', [ModificationController::class, 'search'])->name('search');
            Route::post('/rename/{modification}', [ModificationController::class, 'rename'])->name('rename');
            Route::post('/add-product/{modification}', [ModificationController::class, 'add_product'])->name('add-product');
            Route::delete('/del-product/{modification}', [ModificationController::class, 'del_product'])->name('del-product');
        });
        //PARSER
        Route::group([
            'prefix' => 'parser',
            'as' => 'parser.',
        ], function () {
            Route::get('/', [ParserController::class, 'index'])->name('index');
            //Route::get('/show/{parser}', [ParserController::class, 'show'])->name('show');
            Route::post('/block/{parser}', [ParserController::class, 'block'])->name('block');
            Route::post('/fragile/{parser}', [ParserController::class, 'fragile'])->name('fragile');
            Route::post('/sanctioned/{parser}', [ParserController::class, 'sanctioned'])->name('sanctioned');
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
            Route::post('/add/{product}', [ProductController::class, 'add_image'])->name('add');
            Route::post('/get/{product}', [ProductController::class, 'get_images'])->name('get');
            Route::delete('/del/{product}', [ProductController::class, 'del_image'])->name('del');
            //Route::post('/up/{product}', [ProductController::class, 'up_image'])->name('up');
            //Route::post('/down/{product}', [ProductController::class, 'down_image'])->name('down');
            Route::post('/set/{product}', [ProductController::class, 'set_image'])->name('set');
            Route::post('/move/{product}', [ProductController::class, 'move_image'])->name('move');
        });
        Route::group([
            'prefix' => 'edit',
            'as' => 'edit.'
        ], function () {
            Route::post('/common/{product}', [ProductController::class, 'edit_common'])->name('common');
            Route::post('/description/{product}', [ProductController::class, 'edit_description'])->name('description');
            Route::post('/dimensions/{product}', [ProductController::class, 'edit_dimensions'])->name('dimensions');
            Route::post('/video/{product}', [ProductController::class, 'edit_video'])->name('video');
            Route::post('/attribute/{product}', [ProductController::class, 'edit_attribute'])->name('attribute');
            Route::post('/management/{product}', [ProductController::class, 'edit_management'])->name('management');
            Route::post('/equivalent/{product}', [ProductController::class, 'edit_equivalent'])->name('equivalent');
            Route::post('/related/{product}', [ProductController::class, 'edit_related'])->name('related');
            Route::post('/bonus/{product}', [ProductController::class, 'edit_bonus'])->name('bonus');
            Route::post('/composite/{product}', [ProductController::class, 'edit_composite'])->name('composite');
        });

        Route::post('/rename/{product}', [ProductController::class, 'rename'])->name('rename');
        Route::post('/search', [ProductController::class, 'search'])->name('search');
        Route::post('/search-add', [ProductController::class, 'search_add'])->name('search-add');
        //Route::post('/search_bonus', [ProductController::class, 'search_bonus'])->name('search-bonus');
        Route::post('/attr-modification/{product}', [ProductController::class, 'attr_modification'])->name('attr-modification');
        Route::post('/toggle/{product}', [ProductController::class, 'toggle'])->name('toggle');
        Route::post('/sale/{product}', [ProductController::class, 'sale'])->name('sale');
        Route::post('/restore/{id}', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/full-delete/{id}', [ProductController::class, 'full_delete'])->name('full-delete');
        Route::post('/fast-create', [ProductController::class, 'fast_create'])->name('fast-create');

    }
);

Route::resource('product', 'ProductController');
