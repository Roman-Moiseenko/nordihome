<?php

use App\Modules\Parser\Controllers\CategoryParserController;
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
            Route::post('/parser/{product}', [ProductParserController::class, 'parser'])->name('parser');
            Route::post('/by-list/', [ProductParserController::class, 'by_list'])->name('by-list');

        });
        //PRODUCT
 /*       Route::group([
            'prefix' => 'image',
            'as' => 'image.',
        ], function (){
            Route::post('/add/{product}', 'ProductController@add_image')->name('add');
            Route::post('/get/{product}', 'ProductController@get_images')->name('get');
            Route::delete('/del/{product}', 'ProductController@del_image')->name('del');
            Route::post('/up/{product}', 'ProductController@up_image')->name('up');
            Route::post('/down/{product}', 'ProductController@down_image')->name('down');
            Route::post('/set/{product}', 'ProductController@set_image')->name('set');
            Route::post('/move/{product}', 'ProductController@move_image')->name('move');
        });
        */

    }
);


