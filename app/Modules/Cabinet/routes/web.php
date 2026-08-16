<?php

use App\Modules\Cabinet\Presentation\Http\Controllers\CabinetController;
use App\Modules\Cabinet\Presentation\Http\Controllers\OptionsController;
use App\Modules\Cabinet\Presentation\Http\Controllers\OrderController;
use App\Modules\Cabinet\Presentation\Http\Controllers\ReviewController;
use App\Modules\Cabinet\Presentation\Http\Controllers\WishController;
use Illuminate\Support\Facades\Route;


Route::group([
    'as' => 'cabinet.',
    'prefix' => 'cabinet',
    //'namespace' => 'Cabinet',
    'middleware' => ['user_cookie_id', 'auth', 'role:client'],
],
    function () {
        Route::get('/', [CabinetController::class, 'view'])->name('view');
        Route::get('/profile', [CabinetController::class, 'profile'])->name('profile');
        Route::post('/fullname/{user}', [CabinetController::class, 'fullname'])->name('fullname');
        Route::post('/phone/{user}', [CabinetController::class, 'phone'])->name('phone');
        Route::post('/email/{user}', [CabinetController::class, 'email'])->name('email');
        Route::post('/password/{user}', [CabinetController::class, 'password'])->name('password');

        Route::group([
            'as' => 'options.',
            'prefix' => 'options',
        ], function () {
            Route::get('/', [OptionsController::class, 'index'])->name('index');
            Route::post('/subscription/{subscription}', [OptionsController::class, 'subscription'])->name('subscription');

        });

        Route::group([
            'as' => 'wish.',
            'prefix' => 'wish'
        ], function () {
            Route::get('/', [WishController::class, 'index'])->name('index');
            Route::post('/clear', [WishController::class, 'clear'])->name('clear');
            Route::post('/get', [WishController::class, 'get'])->name('get');
            Route::post('/toggle/{product}', [WishController::class, 'toggle'])->name('toggle');
        });

        Route::group([
            'as' => 'order.',
            'prefix' => 'order'
        ], function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'view'])->name('view');
            Route::get('/{order}/copy', [OrderController::class, 'copy'])->name('copy');
            Route::get('/new/{id}', [OrderController::class, 'new_order'])->name('new_order');
        });
        Route::group([
            'as' => 'review.',
            'prefix' => 'review',
        ], function() {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/show/{id}', [ReviewController::class, 'show'])->name('show');

        });
    }
);
