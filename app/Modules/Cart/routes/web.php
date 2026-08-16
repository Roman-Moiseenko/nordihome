<?php

use App\Modules\Cart\Presentation\Http\Controllers\Web\CartController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin',
    'as' => 'admin.',
],function () {
    //Маршруты тут для админки
});

//Клиентские маршруты

Route::group(
    [
        'as' => 'shop.',
        'middleware' => ['user_cookie_id'],
    ],
    function () {
        Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
        Route::group([
            'prefix' => 'cart_post',
            'as' => 'cart.',
        ], function () {
            Route::post('/cart', [CartController::class, 'cart'])->name('all');
            Route::post('/add', [CartController::class, 'add'])->name('add');
            //     Route::post('/sub/{product}', [CartController::class, 'sub'])->name('sub');
            //     Route::post('/set/{product}', [CartController::class, 'set'])->name('set');
            //      Route::post('/check/{product}', [CartController::class, 'check'])->name('check');
            //    Route::post('/check-all', [CartController::class, 'check_all'])->name('check-all');
            Route::post('/remove/{product}', [CartController::class, 'remove'])->name('remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        });
    }
);
