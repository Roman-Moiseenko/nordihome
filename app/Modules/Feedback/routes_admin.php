<?php

use App\Modules\Feedback\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'feedback',
    'as' => 'feedback.',
    //'namespace' => 'Feedback',
], function() {
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
