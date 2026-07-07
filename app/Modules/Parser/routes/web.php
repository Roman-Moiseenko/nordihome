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
        Route::get('/{id}', [ProductParserController::class, 'show'])->name('show');
        Route::post('/parser/{id}', [ProductParserController::class, 'parser'])->name('parser');
        Route::post('/available/{id}', [ProductParserController::class, 'available'])->name('available');
        Route::post('/fragile/{id}', [ProductParserController::class, 'fragile'])->name('fragile');
        Route::post('/sanctioned/{id}', [ProductParserController::class, 'sanctioned'])->name('sanctioned');

    });
    //LOG
    Route::group([
        'prefix' => 'log',
        'as' => 'log.',
    ], function (){
        Route::get('/', [ParserLogController::class, 'index'])->name('index');
        Route::get('/{id}', [ParserLogController::class, 'show'])->name('show');
        Route::post('/read/{id}', [ParserLogController::class, 'read'])->name('read');
    });
});
