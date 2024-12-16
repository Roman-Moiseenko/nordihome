<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'discount',
        'as' => 'discount.',
    ],
    function () {
        Route::group([
            'prefix' => 'promotion',
            'as' => 'promotion.',
        ], function () {
            Route::post('/add-product/{promotion}', 'PromotionController@add_product')->name('add-product');
            Route::post('/add-products/{promotion}', 'PromotionController@add_products')->name('add-products');
            Route::post('/set-product/{promotion}', 'PromotionController@set_product')->name('set-product');
            Route::delete('/del-product/{promotion}', 'PromotionController@del_product')->name('del-product');
            Route::post('/set-info/{promotion}', 'PromotionController@set_info')->name('set-info');

            Route::post('/toggle/{promotion}', 'PromotionController@toggle')->name('toggle');
            Route::post('/stop/{promotion}', 'PromotionController@stop')->name('stop');
            Route::post('/start/{promotion}', 'PromotionController@start')->name('start');
        });
        Route::group([
            'prefix' => 'discount',
            'as' => 'discount.',
        ], function () {
            Route::post('/widget', 'DiscountController@widget')->name('widget');
            Route::post('/published/{discount}', 'DiscountController@published')->name('published');
            Route::post('/toggle/{discount}', 'DiscountController@toggle')->name('toggle');
            Route::post('/set-info/{discount}', 'DiscountController@set_info')->name('set-info');
        });



        Route::resource('promotion', 'PromotionController'); //CRUD
        Route::resource('discount', 'DiscountController'); //CRUD
        //Route::resource('coupon', 'CouponController'); //CRUD
    }
);
