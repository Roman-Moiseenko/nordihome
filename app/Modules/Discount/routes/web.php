<?php

use App\Modules\Discount\Controllers\DiscountController;
use App\Modules\Discount\Presentation\Http\Controllers\Web\PromotionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/discount',
    'as' => 'admin.discount.',
],function () {
    Route::group([
        'prefix' => 'promotion',
        'as' => 'promotion.',
    ], function () {
        Route::post('/add-product/{promotion}', [PromotionController::class, 'add_product'])->name('add-product');
        Route::post('/add-products/{promotion}', [PromotionController::class, 'add_products'])->name('add-products');
        Route::post('/set-product/{promotion}', [PromotionController::class, 'set_product'])->name('set-product');
        Route::delete('/del-product/{promotion}', [PromotionController::class, 'del_product'])->name('del-product');
        Route::post('/set-info/{id}', [PromotionController::class, 'setInfo'])->name('set-info');

        Route::post('/draft/{id}', [PromotionController::class, 'draft'])->name('draft');
        Route::post('/waiting/{id}', [PromotionController::class, 'waiting'])->name('waiting');
        Route::post('/stop/{id}', [PromotionController::class, 'stop'])->name('stop');
        Route::post('/start/{id}', [PromotionController::class, 'start'])->name('start');
    });
    Route::group([
        'prefix' => 'discount',
        'as' => 'discount.',
    ], function () {
        Route::post('/widget', [DiscountController::class, 'widget'])->name('widget');
        //Route::post('/published/{discount}', [DiscountController::class, 'published'])->name('published');
        Route::post('/toggle/{discount}', [DiscountController::class, 'toggle'])->name('toggle');
        Route::post('/set-info/{discount}', [DiscountController::class, 'set_info'])->name('set-info');
    });



    Route::resource('promotion', PromotionController::class); //CRUD
    Route::resource('discount', DiscountController::class); //CRUD
    //Route::resource('coupon', 'CouponController'); //CRUD

});
