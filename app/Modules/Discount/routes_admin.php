<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'discount',
        'as' => 'discount.',
        //'namespace' => 'Discount',
    ],
    function () {
        Route::group([
            'prefix' => 'promotion',
            'as' => 'promotion.',
        ], function () {
            //Route::post('/{promotion}/add-group', 'PromotionController@add_group')->name('add-group');
            Route::post('/add-product/{promotion}', 'PromotionController@add_product')->name('add-product');
            Route::post('/add-products/{promotion}', 'PromotionController@add_products')->name('add-products');
            Route::post('/{promotion}/set-product/{product}', 'PromotionController@set_product')->name('set-product');
            Route::delete('/{promotion}/del-product/{product}', 'PromotionController@del_product')->name('del-product');

            Route::delete('/{promotion}/del-group/{group}', 'PromotionController@del_group')->name('del-group');
            Route::post('/published/{promotion}', 'PromotionController@published')->name('published');
            Route::post('/draft/{promotion}', 'PromotionController@draft')->name('draft');
            Route::post('/stop/{promotion}', 'PromotionController@stop')->name('stop');
            Route::post('/start/{promotion}', 'PromotionController@start')->name('start');
        });

        Route::post('/discount/widget', 'DiscountController@widget')->name('discount.widget');
        Route::post('/discount/{discount}/published', 'DiscountController@published')->name('discount.published');
        Route::post('/discount/{discount}/draft', 'DiscountController@draft')->name('discount.draft');

        Route::resource('promotion', 'PromotionController'); //CRUD
        Route::resource('discount', 'DiscountController'); //CRUD
        //Route::resource('coupon', 'CouponController'); //CRUD
    }
);
