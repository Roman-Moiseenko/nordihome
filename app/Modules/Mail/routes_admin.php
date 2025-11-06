<?php

use App\Modules\Mail\Controllers\InboxController;
use App\Modules\Mail\Controllers\OutboxController;
use App\Modules\Mail\Controllers\SystemMailController;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        'prefix' => 'mail',
        'as' => 'mail.'
    ],
    function() {
        Route::group([
            'prefix' => 'system',
            'as' => 'system.'
        ], function() {
            Route::get('/attachment', [SystemMailController::class, 'attachment'])->name('attachment');
            Route::post('/repeat/{system}', [SystemMailController::class, 'repeat'])->name('repeat');
        });
        Route::group([
            'prefix' => 'outbox',
            'as' => 'outbox.'
        ], function() {
            Route::get('/attachment', [OutboxController::class, 'attachment'])->name('attachment');
            //Route::post('/repeat/{outbox}', [OutboxController::class, 'repeat'])->name('repeat');
            Route::post('/send/{outbox}', [OutboxController::class, 'send'])->name('send');
            Route::post('/delete-attachment/{outbox}', [OutboxController::class, 'delete_attachment'])->name('delete-attachment');
        });
        Route::group([
            'prefix' => 'inbox',
            'as' => 'inbox.'
        ], function() {
            Route::get('/attachment', [InboxController::class, 'attachment'])->name('attachment');
            Route::get('/reply/{inbox}', [InboxController::class, 'reply'])->name('reply');
            Route::get('/load', [InboxController::class, 'load'])->name('load');
        });

        Route::Resource('system', 'SystemMailController')->only(['index', 'show']);
        Route::Resource('inbox', 'InboxController')->only(['index', 'show', 'destroy']);
        Route::Resource('outbox', 'OutboxController');

    }
);
