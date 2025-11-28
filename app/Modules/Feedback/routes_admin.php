<?php

use App\Modules\Feedback\Controllers\FormController;
use App\Modules\Feedback\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'feedback',
    'as' => 'feedback.',
    //'namespace' => 'Feedback',
], function() {

    Route::group([
        'prefix' => 'form',
        'as' => 'form.',
    ], function () {
        Route::get('/', [FormController::class, 'index'])->name('index');
        Route::post('/{widget}', [FormController::class, 'from_shop'])->name('from-shop');
        Route::post('/get/{widget}', [FormController::class, 'get_url']);
    });


    Route::group([
        'prefix' => 'review',
        'as' => 'review.',
    ], function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
        Route::post('/published/{review}', [ReviewController::class, 'published'])->name('published');
        Route::post('/blocked/{review}', [ReviewController::class, 'blocked'])->name('blocked');
    });

});
