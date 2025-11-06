<?php

use App\Modules\Order\Controllers\ExpenseController;
use App\Modules\Order\Controllers\OrderController;
use App\Modules\Order\Controllers\PaymentController;
use App\Modules\Order\Controllers\ProductController;
use App\Modules\Order\Controllers\RefundController;
use App\Modules\Order\Controllers\ReserveController;
use Illuminate\Support\Facades\Route;


//Заказы
Route::group(
    [
        'prefix' => 'order',
        'as' => 'order.',
    ],
    function () {
        Route::post('/set-created/{order}', [OrderController::class, 'set_created'])->name('set-created');
        Route::post('/copy/{order}', [OrderController::class, 'copy'])->name('copy');

        Route::post('/movement/{order}', [OrderController::class, 'movement'])->name('movement');
        Route::post('/expense-calculate/{order}', [OrderController::class, 'expense_calculate'])->name('expense-calculate');
        Route::post('/invoice/{order}', [OrderController::class, 'invoice'])->name('invoice');
        //Route::post('/send-invoice/{order}', [OrderController::class, 'send_invoice'])->name('send-invoice');
        Route::post('/set-info/{order}', [OrderController::class, 'set_info'])->name('set-info');
        //Route::post('/resend-invoice/{order}', [OrderController::class, 'resend_invoice'])->name('resend-invoice');

        Route::post('/reserve-collect/{item}', [OrderController::class, 'reserve_collect'])->name('reserve-collect');
    //    Route::post('/set-info/{order}', 'OrderController@set_info')->name('set-info');
        Route::post('/add-products/{order}', [OrderController::class, 'add_products'])->name('add-products');
        Route::post('/add-product/{order}', [OrderController::class, 'add_product'])->name('add-product');

        Route::post('/set-item/{item}', [OrderController::class, 'set_item'])->name('set-item');
        Route::delete('/del-item/{item}', [OrderController::class, 'del_item'])->name('del-item');
        Route::post('/set-user/{order}', [OrderController::class, 'set_user'])->name('set-user');
        Route::post('/set-assemblage', [OrderController::class, 'set_assemblage'])->name('set-assemblage');
        Route::post('/set-packing', [OrderController::class, 'set_packing'])->name('set-packing');

        Route::post('/set-discount/{order}', [OrderController::class, 'set_discount'])->name('set-discount');

        Route::post('/add-addition/{order}', [OrderController::class, 'add_addition'])->name('add-addition');
        Route::post('/set-addition/{addition}', [OrderController::class, 'set_addition'])->name('set-addition');
        Route::delete('/del-addition/{addition}', [OrderController::class, 'del_addition'])->name('del-addition');


        Route::post('/set-manager/{order}', [OrderController::class, 'set_manager'])->name('set-manager');
        Route::post('/set-reserve/{order}', [OrderController::class, 'set_reserve'])->name('set-reserve');
        Route::post('/set-comment/{order}', [OrderController::class, 'set_comment'])->name('set-comment');

        Route::post('/cancel/{order}', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/awaiting/{order}', [OrderController::class, 'awaiting'])->name('awaiting');
        Route::post('/work/{order}', [OrderController::class, 'work'])->name('work');

        Route::post('/search-user', [OrderController::class, 'search_user'])->name('search-user');

        Route::get('/log/{order}', [OrderController::class, 'log'])->name('log');
        Route::post('/take/{order}', [OrderController::class, 'take'])->name('take');

        //Распоряжения
        Route::group(
            [
                'prefix' => 'expense',
                'as' => 'expense.',
            ],
            function () {
                Route::post('/create/{order}', [ExpenseController::class, 'create'])->name('create');
                //Route::post('/issue_shop', [ExpenseController::class, 'issue_shop'])->name('issue-shop');
                //Route::post('/issue_warehouse', [ExpenseController::class, 'issue_warehouse'])->name('issue-warehouse');
                Route::get('/show/{expense}', [ExpenseController::class, 'show'])->name('show');
                Route::post('/set-delivery/{expense}', [ExpenseController::class, 'set_delivery'])->name('set-delivery');
                Route::post('/set-honest/{expense}', [ExpenseController::class, 'set_honest'])->name('set-honest');

                Route::post('/canceled/{expense}', [ExpenseController::class, 'canceled'])->name('canceled');
                Route::post('/set-info/{expense}', [ExpenseController::class, 'set_info'])->name('set-info');
                Route::post('/assembly/{expense}', [ExpenseController::class, 'assembly'])->name('assembly');
                Route::post('/trade12/{expense}', [ExpenseController::class, 'trade12'])->name('trade12');
            }
        );
        //Возвраты
        Route::group(
            [
                'prefix' => 'refund',
                'as' => 'refund.',
            ],
            function () {
                Route::post('/completed/{refund}', [RefundController::class, 'completed'])->name('completed');
                Route::post('/throw/{refund}', [RefundController::class, 'throw'])->name('throw');
                Route::post('/set-info/{refund}', [RefundController::class, 'set_info'])->name('set-info');
                Route::post('/set-item/{item}', [RefundController::class, 'set_item'])->name('set-item');
                Route::delete('/del-item/{item}', [RefundController::class, 'del_item'])->name('del-item');
                Route::post('/set-addition/{addition}', [RefundController::class, 'set_addition'])->name('set-addition');
                Route::delete('/del-addition/{addition}', [RefundController::class, 'del_addition'])->name('del-addition');

                Route::get('/show/{refund}', [RefundController::class, 'show'])->name('show');
                Route::post('/store/{expense}', [RefundController::class, 'store'])->name('store');
                Route::get('/', [RefundController::class, 'index'])->name('index');
            }
        );
        //Товары
        Route::group(
            [
                'prefix' => 'product',
                'as' => 'product.',
            ],
            function () {
                Route::get('/', [ProductController::class, 'index'])->name('index');
               // Route::post('/show/{product}', [ProductController::class, 'show'])->name('show');
            }
        );
        //Платежи

        Route::group(
            [
                'prefix' => 'payment',
                'as' => 'payment.',
            ],
            function () {
                Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
                Route::post('/find', [PaymentController::class, 'find'])->name('find');
                Route::post('/set-order/{order}/{payment}', [PaymentController::class, 'set_order'])->name('set-order');

                Route::post('/{order}', [PaymentController::class, 'create'])->name('create');
                Route::post('/set-info/{payment}', [PaymentController::class, 'set_info'])->name('set-info');
                Route::post('/completed/{payment}', [PaymentController::class, 'completed'])->name('completed');
                Route::post('/work/{payment}', [PaymentController::class, 'work'])->name('work');
                Route::post('/create-refund/{refund}', [PaymentController::class, 'create_refund'])->name('create-refund');
                Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
                Route::get('/', [PaymentController::class, 'index'])->name('index');
            }
        );
      //  Route::resource('payment', 'PaymentController')->only(['index', 'show']);
        //Резерв
        Route::get('/reserve', [ReserveController::class, 'index'])->name('reserve.index');
    }
);

Route::resource('order', 'OrderController')->except(['destroy']);
