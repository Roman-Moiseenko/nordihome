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
        Route::post('/settings', [AvitoController::class, 'updateSettings'])->name('settings.update');
        Route::get('/operations', [AvitoController::class, 'operations'])->name('operations');

        Route::get('/registry', [AvitoController::class, 'registry'])->name('registry');
        Route::post('/registry/import', [AvitoController::class, 'importRegistry'])->name('registry.import');
        Route::post('/registry/{id}', [AvitoController::class, 'updateRegistryEntry'])->name('registry.update');

        Route::get('/common-images', [AvitoController::class, 'commonImages'])->name('common-images');
        Route::get('/common-images/list', [AvitoController::class, 'commonImageList'])->name('common-images.list');
        Route::post('/common-images', [AvitoController::class, 'storeCommonImage'])->name('common-images.store');
        Route::post('/common-images/sort', [AvitoController::class, 'sortCommonImages'])->name('common-images.sort');
        Route::post('/common-images/{id}', [AvitoController::class, 'updateCommonImage'])->name('common-images.update');
        Route::delete('/common-images/{id}', [AvitoController::class, 'deleteCommonImage'])->name('common-images.destroy');

        Route::post('/sync-categories', [AvitoController::class, 'syncCategories'])->name('sync-categories');
        Route::post('/sync-fields', [AvitoController::class, 'syncFields'])->name('sync-fields');

        Route::post('/import', [AvitoController::class, 'uploadStructure'])->name('import');
        Route::get('/category/{id}/fields', [AvitoController::class, 'categoryFields'])->name('category.fields');
        Route::post('/category/{id}/fields/{fieldId}/type', [AvitoController::class, 'updateFieldType'])->name('category.field.type');
        Route::post('/category/{id}/fields/{fieldId}/variants', [AvitoController::class, 'updateFieldVariants'])->name('category.field.variants');

        Route::get('/product/{id}', [AvitoController::class, 'productShow'])->name('product.show');
        Route::post('/product', [AvitoController::class, 'store'])->name('product.store');
        Route::post('/product/{id}', [AvitoController::class, 'update'])->name('product.update');
        Route::post('/product/{id}/values', [AvitoController::class, 'updateValues'])->name('product.values');
        Route::post('/product/{id}/description', [AvitoController::class, 'updateDescription'])->name('product.description');
        Route::post('/product/{id}/category', [AvitoController::class, 'setCategory'])->name('product.category');
        Route::post('/product/{id}/attach-product', [AvitoController::class, 'attachProduct'])->name('product.attach-product');
        Route::post('/product/{id}/detach-product', [AvitoController::class, 'detachProduct'])->name('product.detach-product');
        Route::post('/product/{id}/copy-photo', [AvitoController::class, 'copyPhoto'])->name('product.copy-photo');
        Route::post('/product/{id}/image', [AvitoController::class, 'uploadImage'])->name('product.image');
        Route::post('/product/{id}/copy-common-image', [AvitoController::class, 'copyCommonImage'])->name('product.copy-common-image');
        Route::post('/product/{id}/delivery', [AvitoController::class, 'toggleDelivery'])->name('product.delivery');
        Route::post('/product/{id}/active', [AvitoController::class, 'setActive'])->name('product.active');
    });
});
