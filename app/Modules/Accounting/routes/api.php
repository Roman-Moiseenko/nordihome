<?php

// use Illuminate\Support\Facades\Route;

// Route::middleware([])->prefix('accounting')->group(function () {

//     Route::get('/api', function () {
//         return 'accounting';
//     });

// });

use App\Modules\Accounting\Presentation\Http\Controllers\Web\ExchangeController;

Route::group([
    'prefix' => 'exchange',
    'as' => 'exchange.',
],
    function () {
        Route::post('/stock', [ExchangeController::class, 'stock'])->name('stock');
        Route::post('/price', [ExchangeController::class, 'price'])->name('price');
    }
);

