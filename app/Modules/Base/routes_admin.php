<?php

use App\Modules\Base\Controllers\FileController;
use App\Modules\Base\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'file',
        'as' => 'file.',
    ],
    function () {
        Route::any('/download', [FileController::class, 'download'])->name('download');
        Route::post('/remove-file', [FileController::class, 'remove_file'])->name('remove-file');
    }
);

Route::post('/report', [ReportController::class, 'report'])->name('report');
Route::get('/test', [FileController::class, 'test'])->name('test');
