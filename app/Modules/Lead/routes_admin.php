<?php

use App\Modules\Feedback\Controllers\FormController;
use App\Modules\Lead\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'lead',
        'as' => 'lead.',
    ], function () {

    Route::get('/', [LeadController::class, 'index'])->name('index');
    Route::post('/set-status/{lead}', [LeadController::class, 'set_status'])->name('set-status');
    Route::post('/add-item/{lead}', [LeadController::class, 'add_item'])->name('add-item');
    Route::post('/set-name/{lead}', [LeadController::class, 'set_name'])->name('set-name');
    Route::post('/set-comment/{lead}', [LeadController::class, 'set_comment'])->name('set-comment');
    Route::post('/set-finished/{lead}', [LeadController::class, 'set_finished'])->name('set-finished');
    Route::post('/canceled/{lead}', [LeadController::class, 'canceled'])->name('canceled');
    //Route::post('/completed/{lead}', [LeadController::class, 'completed'])->name('completed');

    Route::post('/create-user/{lead}', [LeadController::class, 'create_user'])->name('create-user');
    Route::post('/create-order/{lead}', [LeadController::class, 'create_order'])->name('create-order');


});

