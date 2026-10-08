<?php

use App\Modules\Catalog\Controllers\AttributeController;
use App\Modules\Catalog\Controllers\OnOrderController;
use App\Modules\Catalog\Controllers\ParserController;
use App\Modules\Catalog\Controllers\PriorityController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\AttributeGroupController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\BrandController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\CategoryController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\CategoryProductController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\EquivalentController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\GroupController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\GroupProductController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\ModificationController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\ProductController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\ProductEditController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\RoomController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\RoomProductController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\SeriesController;
use App\Modules\Catalog\Presentation\Http\Controllers\Web\TagController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/catalog',
    'as' => 'admin.catalog.',
], function () {

    Route::post('/action', [ProductController::class, 'action'])->name('action');
    Route::post('/upload', [ProductController::class, 'upload'])->name('product.upload');
    Route::post('/find-parser', [ProductController::class, 'find_parser'])->name('product.find-parser');
    //Атрибуты
    Route::group([
        'prefix' => 'attribute',
        'as' => 'attribute.',
    ], function () {
        Route::get('/types', [AttributeController::class, 'types'])->name('types');
    });

    //Группы атрибутов
    Route::group([
        'prefix' => 'attribute-group',
        'as' => 'attribute-group.',
    ], function () {
        Route::get('/list', [AttributeGroupController::class, 'list'])->name('list');
        Route::post('/sort', [AttributeGroupController::class, 'sort'])->name('sort');
    });
    Route::resource('attribute-group', AttributeGroupController::class)
        ->except(['create', 'edit'])
        ->parameters(['attribute_group' => 'id']);

    //BRAND
    Route::group([
        'prefix' => 'brand',
        'as' => 'brand.',
    ], function () {
        Route::get('/list', [BrandController::class, 'list'])->name('list');
        // Товары бренда (прямая связь brand_id в products)
        Route::get('/{id}/products', [BrandController::class, 'brandProducts'])->name('products');

    });
    //CATEGORY
    Route::group([
        'prefix' => 'category',
        'as' => 'category.',
    ], function () {
        Route::post('/up/{id}', [CategoryController::class, 'up'])->name('up');
        Route::post('/down/{id}', [CategoryController::class, 'down'])->name('down');
        Route::post('/move/{id}', [CategoryController::class, 'move'])->name('move');
        Route::get('/tree', [CategoryController::class, 'tree'])->name('tree');
        Route::post('/toggle/{id}', [CategoryController::class, 'toggle'])->name('toggle');
        Route::get('/products/{id}', [CategoryController::class, 'products'])->name('products');
        Route::get('/attributes/{id}', [CategoryController::class, 'attributes'])->name('attributes');
        // Связь Category → Products (через pivot)
        Route::get('/{id}/products/second', [CategoryProductController::class, 'categoryProducts'])->name('products.second');
        Route::post('/{id}/products/sync', [CategoryProductController::class, 'assignCategoryProducts'])->name('products.sync');
        Route::post('/{id}/products/attach', [CategoryProductController::class, 'attachCategoryProducts'])->name('products.attach');
        Route::delete('/{id}/products/detach', [CategoryProductController::class, 'detachCategoryProducts'])->name('products.detach');
    });
    Route::resource('category', CategoryController::class)->parameters(['category' => 'id']); //CRUD
    //ROOMS
    Route::group([
        'prefix' => 'room',
        'as' => 'room.',
    ], function () {
        Route::get('/tree', [RoomController::class, 'tree'])->name('tree');
        Route::post('/up/{id}', [RoomController::class, 'up'])->name('up');
        Route::post('/down/{id}', [RoomController::class, 'down'])->name('down');
        Route::post('/move/{id}', [RoomController::class, 'move'])->name('move');
        Route::post('/toggle/{id}', [RoomController::class, 'toggle'])->name('toggle');
        // Связь Room → Products
        Route::get('/{id}/products', [RoomProductController::class, 'roomProducts'])->name('products');
        Route::post('/{id}/products/sync', [RoomProductController::class, 'assignRoomProducts'])->name('products.sync');
        Route::post('/{id}/products/attach', [RoomProductController::class, 'attachRoomProducts'])->name('products.attach');
        Route::delete('/{id}/products/detach', [RoomProductController::class, 'detachRoomProducts'])->name('products.detach');
    });
    Route::resource('room', RoomController::class)->except(['create', 'edit']); //CRUD
    //TAG
    Route::group([
        'prefix' => 'tag',
        'as' => 'tag.',
    ], function () {
        Route::get('/list', [TagController::class, 'list'])->name('list');
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::get('/{id}', [TagController::class, 'show'])->name('show');
        Route::post('/store', [TagController::class, 'store'])->name('store');
        Route::post('/update/{id}', [TagController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [TagController::class, 'destroy'])->name('destroy');

    });
    //EQUIVALENT
    Route::group([
        'prefix' => 'equivalent',
        'as' => 'equivalent.',
    ], function () {
        // Связь Equivalent → Products (через pivot)
        Route::get('/{id}/products', [EquivalentController::class, 'products'])->name('products');
        Route::post('/{id}/products/sync', [EquivalentController::class, 'assignProducts'])->name('products.sync');
        Route::post('/{id}/products/attach', [EquivalentController::class, 'attachProducts'])->name('products.attach');
        Route::delete('/{id}/products/detach', [EquivalentController::class, 'detachProducts'])->name('products.detach');
    });
    //Группа товаров
    Route::group([
        'prefix' => 'group',
        'as' => 'group.',
    ], function () {
        Route::get('/list', [GroupController::class, 'list'])->name('list');
        //Route::post('/search/{group}', [GroupController::class, 'search'])->name('search');

        // Связь Group → Products (через pivot)
        Route::get('/{id}/products', [GroupProductController::class, 'groupProducts'])->name('products');
        Route::post('/{id}/products/sync', [GroupProductController::class, 'assignGroupProducts'])->name('products.sync');
        Route::post('/{id}/products/attach', [GroupProductController::class, 'attachGroupProducts'])->name('products.attach');
        Route::delete('/{id}/products/detach', [GroupProductController::class, 'detachGroupProducts'])->name('products.detach');
    });
    //Серия товаров
    Route::group([
        'prefix' => 'series',
        'as' => 'series.',
    ], function () {
        Route::get('/list', [SeriesController::class, 'list'])->name('list');
        Route::post('/add-product/{id}', [SeriesController::class, 'add_product'])->name('add-product');
        Route::post('/add-products/{id}', [SeriesController::class, 'add_products'])->name('add-products');
        Route::delete('/del-product/{id}', [SeriesController::class, 'del_product'])->name('del-product');
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
    /*
    Route::group([
        'prefix' => 'reduced',
        'as' => 'reduced.',
    ], function () {
        Route::get('/', [ReducedController::class, 'index'])->name('index');
        Route::post('/add-product', [ReducedController::class, 'add_product'])->name('add-product');
        Route::post('/add-products', [ReducedController::class, 'add_products'])->name('add-products');
        Route::delete('/del-product/{product}', [ReducedController::class, 'del_product'])->name('del-product');
    });
    */
    //Только под заказ
    Route::group([
        'prefix' => 'on-order',
        'as' => 'on-order.',
    ], function () {
        Route::get('/', [OnOrderController::class, 'index'])->name('index');
        Route::post('/add-product', [OnOrderController::class, 'add_product'])->name('add-product');
        Route::post('/add-products', [OnOrderController::class, 'add_products'])->name('add-products');
        Route::delete('/del-product/{product}', [OnOrderController::class, 'del_product'])->name('del-product');
    });

    //MODIFICATION
    Route::group([
        'prefix' => 'modification',
        'as' => 'modification.',
    ], function () {
        //Route::post('/set-modifications/{modification}', [ModificationController::class, 'set_modifications'])->name('set-modifications');
        Route::post('/set-primary/{id}', [ModificationController::class, 'setPrimary'])->name('set-primary');

        Route::post('/search-create', [ModificationController::class, 'search_create'])->name('search-create');
        Route::post('/search-product/{id}', [ModificationController::class, 'search_product'])->name('search-product');
        Route::post('/rename/{id}', [ModificationController::class, 'rename'])->name('rename');
        Route::post('/add-product/{id}', [ModificationController::class, 'add_product'])->name('add-product');
        Route::delete('/del-product/{id}', [ModificationController::class, 'del_product'])->name('del-product');
    });

    //resource
    Route::resource('brand', BrandController::class)->parameters(['brand' => 'id']); //CRUD

    Route::resource('attribute', AttributeController::class)->parameters(['attribute' => 'id']); //CRUD
    Route::resource('equivalent', EquivalentController::class)->parameters(['equivalent' => 'id']); //CRUD
    Route::resource('group', GroupController::class)->except(['create', 'edit']); //CRUD
    Route::resource('modification', ModificationController::class)->parameters(['modification' => 'id']); //CRUD
    Route::resource('series', SeriesController::class)->except(['create', 'edit'])->parameters(['series' => 'id']); //CRUD


    //PRODUCT
    Route::group([
        'prefix' => 'product',
        'as' => 'product.'
    ], function () {
        Route::post('/rename/{product}', [ProductController::class, 'rename'])->name('rename');
        Route::post('/search', [ProductController::class, 'search'])->name('search');
        Route::post('/search-add', [ProductController::class, 'search_add'])->name('search-add');
        //Route::post('/search_bonus', [ProductController::class, 'search_bonus'])->name('search-bonus');
        Route::post('/attr-modification/{product}', [ProductController::class, 'attr_modification'])->name('attr-modification');
        Route::post('/toggle/{product}', [ProductController::class, 'toggle'])->name('toggle');
        Route::post('/sale/{product}', [ProductController::class, 'sale'])->name('sale');
        Route::post('/restore/{id}', [ProductController::class, 'restore'])->name('restore');
        Route::delete('/full-delete/{id}', [ProductController::class, 'full_delete'])->name('full-delete');

        // Связь Product → Rooms
        Route::get('/{id}/rooms', [RoomProductController::class, 'productRooms'])->name('rooms');
        Route::post('/{id}/rooms/sync', [RoomProductController::class, 'assignProductRooms'])->name('rooms.sync');
        Route::post('/{id}/rooms/attach', [RoomProductController::class, 'attachProductRooms'])->name('rooms.attach');
        Route::delete('/{id}/rooms/detach', [RoomProductController::class, 'detachProductRooms'])->name('rooms.detach');

        // Связь Product → Category
        Route::get('/{id}/category', [CategoryProductController::class, 'productCategories'])->name('categories');
        Route::post('/{id}/category/sync', [CategoryProductController::class, 'assignProductCategories'])->name('categories.sync');
        Route::post('/{id}/category/attach', [CategoryProductController::class, 'attachProductCategories'])->name('categories.attach');
        Route::delete('/{id}/category/detach', [CategoryProductController::class, 'detachProductCategories'])->name('categories.detach');


        Route::group([
            'prefix' => 'edit',
            'as' => 'edit.'
        ], function () {
            Route::get('/common/{id}', [ProductEditController::class, 'loadCommon'])->name('common');
            Route::post('/common/{id}', [ProductEditController::class, 'saveCommon'])->name('common');
            Route::get('/description/{id}', [ProductEditController::class, 'loadDescription'])->name('description');
            Route::post('/description/{id}', [ProductEditController::class, 'saveDescription'])->name('description');
            Route::get('/dimensions/{id}', [ProductEditController::class, 'loadDimensions'])->name('dimensions');
            Route::post('/dimensions/{id}', [ProductEditController::class, 'saveDimensions'])->name('dimensions');
            Route::get('/video/{id}', [ProductEditController::class, 'loadVideo'])->name('video');
            Route::post('/video/{id}', [ProductEditController::class, 'saveVideo'])->name('video');
            Route::get('/attribute/{id}', [ProductEditController::class, 'loadAttribute'])->name('attribute');
            Route::post('/attribute/{id}', [ProductEditController::class, 'saveAttribute'])->name('attribute');
            Route::get('/management/{id}', [ProductEditController::class, 'loadManagement'])->name('management');
            Route::post('/management/{id}', [ProductEditController::class, 'saveManagement'])->name('management');
            Route::get('/modification/{id}', [ProductEditController::class, 'loadModification'])->name('modification');
            Route::get('/modification-attributes/{id}', [ProductEditController::class, 'loadModificationAttributes'])->name('modification-attributes');
            Route::get('/equivalent/{id}', [ProductEditController::class, 'loadEquivalent'])->name('equivalent');
            Route::post('/equivalent/{id}', [ProductEditController::class, 'saveEquivalent'])->name('equivalent');
            Route::get('/related/{id}', [ProductEditController::class, 'loadRelated'])->name('related');
            Route::post('/related/{id}', [ProductEditController::class, 'saveRelated'])->name('related');
            Route::get('/bonus/{id}', [ProductEditController::class, 'loadBonus'])->name('bonus');
            Route::post('/bonus/{id}', [ProductEditController::class, 'saveBonus'])->name('bonus');
            Route::get('/composite/{id}', [ProductEditController::class, 'loadComposite'])->name('composite');
            Route::post('/composite/{id}', [ProductEditController::class, 'saveComposite'])->name('composite');
        });
    });
    Route::resource('product', ProductController::class)->except(['update'])->parameters(['product' => 'id']);
});
