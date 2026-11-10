<?php

use App\Modules\Marketplace\Presentation\Http\Controllers\Web\AvitoController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/marketplace',
    'as' => 'admin.marketplace.',
], function () {
    Route::group([
        'prefix' => 'avito',
        'as' => 'avito.',
    ], function () {
        Route::get('/', [AvitoController::class, 'index'])->name('index');

        Route::get('/categories', [AvitoController::class, 'categories'])->name('categories');
        Route::get('/settings', [AvitoController::class, 'settings'])->name('settings');
        Route::get('/operations', [AvitoController::class, 'operations'])->name('operations');

        Route::post('/sync-categories', [AvitoController::class, 'syncCategories'])->name('sync-categories');
        Route::post('/sync-fields', [AvitoController::class, 'syncFields'])->name('sync-fields');

        Route::get('/product/{id}', [AvitoController::class, 'productShow'])->name('product.show');
        Route::post('/product', [AvitoController::class, 'store'])->name('product.store');
        Route::post('/product/{id}/delivery', [AvitoController::class, 'toggleDelivery'])->name('product.delivery');
        Route::post('/product/{id}/active', [AvitoController::class, 'setActive'])->name('product.active');
    });
});
