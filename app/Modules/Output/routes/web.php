<?php

use App\Modules\Output\Presentation\Http\Controllers\Admin\FeedController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/output',
    'as' => 'admin.output.',
],function () {
    //FEED
    Route::group([
        'prefix' => 'feed',
        'as' => 'feed.',
    ], function () {
        Route::post('/update/{feed}', [FeedController::class, 'update'])->name('update');

        Route::get('/', [FeedController::class, 'index'])->name('index');
        Route::get('/{id}', [FeedController::class, 'show'])->name('show');
        Route::post('/', [FeedController::class, 'store'])->name('store');
        Route::delete('/{feed}', [FeedController::class, 'destroy'])->name('destroy');
    });
});
