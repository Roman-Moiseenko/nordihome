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
        Route::post('/resend-invoice/{order}', 'OrderController@resend_invoice')->name('resend-invoice');

        Route::post('/set-manager/{order}', 'OrderController@set_manager')->name('set-manager');
        Route::post('/set-reserve/{order}', 'OrderController@set_reserve')->name('set-reserve');

        Route::post('/canceled/{order}', 'OrderController@canceled')->name('canceled');
        Route::post('/set-awaiting/{order}', 'OrderController@set_awaiting')->name('set-awaiting');

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
                Route::post('/create', 'ExpenseController@create')->name('create');
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
        //Платежи
        Route::resource('payment', 'PaymentController');

        //Резерв
        Route::get('/reserve', 'ReserveController@index')->name('reserve.index');
    }
);

Route::resource('order', 'OrderController')->except(['destroy']);
