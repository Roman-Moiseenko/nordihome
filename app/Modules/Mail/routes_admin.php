<?php

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
            Route::get('/attachment', 'SystemMailController@attachment')->name('attachment');
            Route::post('/repeat/{system}', 'SystemMailController@repeat')->name('repeat');
        });
        Route::group([
            'prefix' => 'outbox',
            'as' => 'outbox.'
        ], function() {
            Route::get('/attachment', 'OutboxController@attachment')->name('attachment');
            Route::post('/repeat/{outbox}', 'OutboxController@repeat')->name('repeat');
            Route::post('/send/{outbox}', 'OutboxController@send')->name('send');
            Route::post('/delete-attachment/{outbox}', 'OutboxController@delete_attachment')->name('delete-attachment');
        });
        Route::group([
            'prefix' => 'inbox',
            'as' => 'inbox.'
        ], function() {
            Route::get('/attachment', 'InboxController@attachment')->name('attachment');
            Route::get('/reply/{inbox}', 'InboxController@reply')->name('reply');
            Route::get('/load', 'InboxController@load')->name('load');
        });

        Route::Resource('system', 'SystemMailController')->only(['index', 'show']);
        Route::Resource('inbox', 'InboxController')->only(['index', 'show', 'destroy']);
        Route::Resource('outbox', 'OutboxController');

    }
);
