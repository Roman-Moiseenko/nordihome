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

});

