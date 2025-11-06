<?php

use App\Modules\Exchange\Controllers\Exchange1CController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'exchange',
    'as'=> 'exchange.'
], function () {
    Route::any('/1c', [Exchange1CController::class, 'web_hook'])->name('1c');
    Route::any('/get-products', [Exchange1CController::class, 'products'])->name('get-products');
});



