<?php

use App\Modules\Bank\Controllers\BankController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'bank',
        'as' => 'bank.',
    ],
    function () {

        Route::post('/upload', [BankController::class, 'upload'])->name('upload');
        Route::post('/currency', [BankController::class, 'currency'])->name('currency');

    });
