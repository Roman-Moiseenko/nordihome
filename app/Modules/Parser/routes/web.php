<?php

use App\Modules\Parser\Presentation\Http\Controllers\Web\CategoryParserController;
use App\Modules\Parser\Presentation\Http\Controllers\Web\ParserLogController;
use App\Modules\Parser\Presentation\Http\Controllers\Web\ProductParserController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/parser',
    'as' => 'admin.parser.',
],function () {
    //CATEGORY
    Route::group([
        'prefix' => 'category',
        'as' => 'category.',
    ], function () {
        Route::post('/toggle/{id}', [CategoryParserController::class, 'toggle'])->name('toggle');
        Route::get('/{category_parser}', [CategoryParserController::class, 'show'])->name('show');
        Route::post('/parser-products/{category_parser}', [CategoryParserController::class, 'parser_products'])->name('parser-products');
        Route::get('/', [CategoryParserController::class, 'index'])->name('index');
        Route::get('/products/{id}', [CategoryParserController::class, 'products'])->name('products');
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
});
