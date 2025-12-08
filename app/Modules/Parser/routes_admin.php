<?php

use App\Modules\Parser\Controllers\CategoryParserController;
use App\Modules\Parser\Controllers\ParserLogController;
use App\Modules\Parser\Controllers\ProductParserController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'parser',
        'as' => 'parser.',
    ],
    function () {

        //CATEGORY
        Route::group([
            'prefix' => 'category',
            'as' => 'category.',
        ], function () {
            Route::post('/toggle/{category_parser}', [CategoryParserController::class, 'toggle'])->name('toggle');

            Route::post('/add-category', [CategoryParserController::class, 'add_category'])->name('add-category');

            //Route::get('/child/{category}', [CategoryParserController::class, 'child'])->name('child');
            Route::get('/{category_parser}', [CategoryParserController::class, 'show'])->name('show');
            Route::delete('/{category_parser}', [CategoryParserController::class, 'destroy'])->name('destroy');
            Route::post('/set-category/{category_parser}', [CategoryParserController::class, 'set_category'])->name('set-category');
            //Route::post('/', [CategoryParserController::class, 'store'])->name('store');
            Route::post('/parser-products/{category_parser}', [CategoryParserController::class, 'parser_products'])->name('parser-products');
            Route::post('/parser-product/{category_parser}', [CategoryParserController::class, 'parser_product'])->name('parser-product');

            Route::get('/', [CategoryParserController::class, 'index'])->name('index');

         //   Route::post('/list', 'CategoryParserController@list')->name('list');
        //    Route::post('/set-info/{category}', 'CategoryParserController@set_info')->name('set-info');
        });

        Route::group([
            'prefix' => 'product',
            'as' => 'product.',
        ], function () {
            Route::get('/', [ProductParserController::class, 'index'])->name('index');
            Route::get('/{product_parser}', [ProductParserController::class, 'show'])->name('show');
            Route::post('/parser/{product_parser}', [ProductParserController::class, 'parser'])->name('parser');
            Route::post('/available/{product_parser}', [ProductParserController::class, 'available'])->name('available');
            Route::post('/fragile/{product_parser}', [ProductParserController::class, 'fragile'])->name('fragile');
            Route::post('/sanctioned/{product_parser}', [ProductParserController::class, 'sanctioned'])->name('sanctioned');
            Route::post('/by-list/', [ProductParserController::class, 'by_list'])->name('by-list');

        });
        //LOG
        Route::group([
            'prefix' => 'log',
            'as' => 'log.',
        ], function (){
            Route::get('/', [ParserLogController::class, 'index'])->name('index');
            Route::get('/{parser_log}', [ParserLogController::class, 'show'])->name('show');
            Route::post('/read/{parser_log}', [ParserLogController::class, 'read'])->name('read');
        });


    }
);


