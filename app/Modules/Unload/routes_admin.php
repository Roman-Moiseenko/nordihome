<?php

use App\Modules\Guide\Controllers\AdditionController;
use App\Modules\Guide\Controllers\CargoCompanyController;
use App\Modules\Guide\Controllers\CountryController;
use App\Modules\Guide\Controllers\GuideController;
use App\Modules\Guide\Controllers\MarkingTypeController;
use App\Modules\Guide\Controllers\MeasuringController;
use App\Modules\Guide\Controllers\VATController;
use App\Modules\Unload\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'unload',
        'as' => 'unload.',
    ], function () {


    //FEED
    Route::group([
        'prefix' => 'feed',
        'as' => 'feed.',
    ], function () {
        Route::post('/add-products/{feed}', [FeedController::class, 'add_products'])->name('add-products');
        Route::post('/add-product/{feed}', [FeedController::class, 'add_product'])->name('add-product');
        Route::post('/del-products/{feed}', [FeedController::class, 'del_products'])->name('del-products');
        Route::post('/del-product/{feed}', [FeedController::class, 'del_product'])->name('del-product');
        Route::post('/add-tag/{feed}', [FeedController::class, 'add_tag'])->name('add-tag');
        Route::post('/del-tag/{feed}', [FeedController::class, 'del_tag'])->name('del-tag');
        Route::post('/categories/{feed}', [FeedController::class, 'categories'])->name('categories');
       // Route::post('/del-category/{feed}', [FeedController::class, 'del_category'])->name('del-category');

        Route::get('/', [FeedController::class, 'index'])->name('index');
        Route::get('/{feed}', [FeedController::class, 'show'])->name('show');
        Route::post('/', [FeedController::class, 'store'])->name('store');
        Route::post('/toggle/{feed}', [FeedController::class, 'toggle'])->name('toggle');
        Route::post('/set-info/{feed}', [FeedController::class, 'set_info'])->name('set-info');
        Route::delete('/{feed}', [FeedController::class, 'destroy'])->name('destroy');
    });

});
