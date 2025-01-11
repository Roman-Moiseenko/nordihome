<?php

use Illuminate\Support\Facades\Route;


//Заказы
Route::group(
    [
        'prefix' => 'order',
        'as' => 'order.',
        //'namespace' => '',
    ],
    function () {
        Route::post('/copy/{order}', 'OrderController@copy')->name('copy');
        //Route::delete('/destroy/{order}', 'OrderController@destroy')->name('destroy');
        Route::post('/movement/{order}', 'OrderController@movement')->name('movement');
        Route::post('/expense-calculate/{order}', 'OrderController@expense_calculate')->name('expense-calculate');
        Route::post('/invoice/{order}', 'OrderController@invoice')->name('invoice');
        Route::post('/send-invoice/{order}', 'OrderController@send_invoice')->name('send-invoice');
        Route::post('/set-info/{order}', 'OrderController@set_info')->name('set-info');
        Route::post('/resend-invoice/{order}', 'OrderController@resend_invoice')->name('resend-invoice');

        Route::post('/reserve-collect/{item}', 'OrderController@reserve_collect')->name('reserve-collect');
    //    Route::post('/set-info/{order}', 'OrderController@set_info')->name('set-info');
        Route::post('/add-product/{order}', 'OrderController@add_product')->name('add-product');
        Route::post('/add-products/{order}', 'OrderController@add_products')->name('add-products');
        Route::post('/set-item/{item}', 'OrderController@set_item')->name('set-item');
        Route::delete('/del-item/{item}', 'OrderController@del_item')->name('del-item');
        Route::post('/set-user/{order}', 'OrderController@set_user')->name('set-user');

        Route::post('/set-discount/{order}', 'OrderController@set_discount')->name('set-discount');

        Route::post('/add-addition/{order}', 'OrderController@add_addition')->name('add-addition');
        Route::post('/set-addition/{addition}', 'OrderController@set_addition')->name('set-addition');
        Route::delete('/del-addition/{addition}', 'OrderController@del_addition')->name('del-addition');


        Route::post('/set-manager/{order}', 'OrderController@set_manager')->name('set-manager');
        Route::post('/set-reserve/{order}', 'OrderController@set_reserve')->name('set-reserve');

        Route::post('/cancel/{order}', 'OrderController@cancel')->name('cancel');
        Route::post('/awaiting/{order}', 'OrderController@awaiting')->name('awaiting');

        Route::post('/search-user', 'OrderController@search_user')->name('search-user');

        Route::get('/log/{order}', 'OrderController@log')->name('log');
        Route::post('/take/{order}', 'OrderController@take')->name('take');

        //Распоряжения
        Route::group(
            [
                'prefix' => 'expense',
                'as' => 'expense.',
            ],
            function () {
                Route::post('/create/{order}', 'ExpenseController@create')->name('create');

                Route::post('/issue_shop', 'ExpenseController@issue_shop')->name('issue-shop');
                Route::post('/issue_warehouse', 'ExpenseController@issue_warehouse')->name('issue-warehouse');
                Route::get('/show/{expense}', 'ExpenseController@show')->name('show');
                Route::delete('/destroy/{expense}', 'ExpenseController@destroy')->name('destroy');
                Route::post('/assembly/{expense}', 'ExpenseController@assembly')->name('assembly');
                Route::post('/trade12/{expense}', 'ExpenseController@trade12')->name('trade12');
            }
        );
        //Возвраты
        Route::group(
            [
                'prefix' => 'refund',
                'as' => 'refund.',
            ],
            function () {

                Route::get('/show/{refund}', 'RefundController@show')->name('show');
                Route::get('/create', 'RefundController@create')->name('create');
                Route::post('/store/{order}', 'RefundController@store')->name('store');
                Route::get('/', 'RefundController@index')->name('index');
            }
        );
        //Товары
        Route::group(
            [
                'prefix' => 'product',
                'as' => 'product.',
            ],
            function () {
                Route::get('/', 'ProductController@index')->name('index');
                Route::post('/show/{product}', 'ProductController@show')->name('show');

            }
        );
        //Платежи
        Route::group(
            [
                'prefix' => 'payment',
                'as' => 'payment.',
            ],
            function () {
                Route::post('/find', 'PaymentController@find')->name('find');
                Route::post('/set-order/{order}/{payment}', 'PaymentController@set_order')->name('set-order');
                Route::get('/{payment}', 'PaymentController@show')->name('show');
                Route::post('/{order}', 'PaymentController@create')->name('create');
                Route::post('/set-info/{payment}', 'PaymentController@set_info')->name('set-info');
                Route::post('/completed/{payment}', 'PaymentController@completed')->name('completed');
                Route::post('/work/{payment}', 'PaymentController@work')->name('work');

                Route::get('/', 'PaymentController@index')->name('index');
            }
        );

        //Резерв
        Route::get('/reserve', 'ReserveController@index')->name('reserve.index');
    }
);

Route::resource('order', 'OrderController')->except(['destroy']);
