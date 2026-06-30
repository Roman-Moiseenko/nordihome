<?php

use App\Modules\Shared\Presentation\Http\Controllers\Web\PhotoController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/photo',
    'as' => 'admin.photo.',
], function () {

    Route::get('/get-by-id/{id}', [PhotoController::class, 'getById'])->name('get-by-id');

    Route::get('/get-by-entity', [PhotoController::class, 'getByEntity'])->name('get-by-entity');

    Route::get('/thumb', [PhotoController::class, 'getThumb'])->name('thumb');

    Route::post('/save-data/{id}', [PhotoController::class, 'saveData'])->name('save-data');

    Route::post('/upload', [PhotoController::class, 'upload'])->name('upload');

    Route::post('/sort/{id}', [PhotoController::class, 'sort'])->name('sort');

    Route::delete('/destroy/{id}', [PhotoController::class, 'destroy'])->name('destroy');
});
