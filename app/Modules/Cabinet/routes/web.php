<?php

use App\Modules\Cabinet\Presentation\Http\Controllers\CabinetAbstractController;
use App\Modules\Cabinet\Presentation\Http\Controllers\OptionsAbstractController;
use App\Modules\Cabinet\Presentation\Http\Controllers\OrderAbstractController;
use App\Modules\Cabinet\Presentation\Http\Controllers\ReviewAbstractController;
use App\Modules\Cabinet\Presentation\Http\Controllers\WishAbstractController;
use Illuminate\Support\Facades\Route;


Route::group([
    'as' => 'cabinet.',
    'prefix' => 'cabinet',
    //'namespace' => 'Cabinet',
    'middleware' => ['auth', 'role:client'],
],
    function () {
        Route::get('/', [CabinetAbstractController::class, 'view'])->name('view');
        Route::get('/profile', [CabinetAbstractController::class, 'profile'])->name('profile');
        Route::post('/fullname/{user}', [CabinetAbstractController::class, 'fullname'])->name('fullname');
        Route::post('/phone/{user}', [CabinetAbstractController::class, 'phone'])->name('phone');
        Route::post('/email/{user}', [CabinetAbstractController::class, 'email'])->name('email');
        Route::post('/password/{user}', [CabinetAbstractController::class, 'password'])->name('password');

        Route::group([
            'as' => 'options.',
            'prefix' => 'options',
        ], function () {
            Route::get('/', [OptionsAbstractController::class, 'index'])->name('index');
            Route::post('/subscription/{subscription}', [OptionsAbstractController::class, 'subscription'])->name('subscription');

        });

        Route::group([
            'as' => 'wish.',
            'prefix' => 'wish'
        ], function () {
            Route::get('/', [WishAbstractController::class, 'index'])->name('index');
            Route::post('/clear', [WishAbstractController::class, 'clear'])->name('clear');
            Route::post('/get', [WishAbstractController::class, 'get'])->name('get');
            Route::post('/toggle/{product}', [WishAbstractController::class, 'toggle'])->name('toggle');
        });

        Route::group([
            'as' => 'order.',
            'prefix' => 'order'
        ], function () {
            Route::get('/', [OrderAbstractController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderAbstractController::class, 'view'])->name('view');
            Route::get('/{order}/copy', [OrderAbstractController::class, 'copy'])->name('copy');
            Route::get('/new/{id}', [OrderAbstractController::class, 'new_order'])->name('new_order');
        });
        Route::group([
            'as' => 'review.',
            'prefix' => 'review',
        ], function() {
            Route::get('/', [ReviewAbstractController::class, 'index'])->name('index');
            Route::get('/show/{id}', [ReviewAbstractController::class, 'show'])->name('show');

        });
    }
);
