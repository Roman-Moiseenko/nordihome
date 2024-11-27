<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'file',
        'as' => 'file.',
    ],
    function () {
        Route::get('/download', 'FileController@download')->name('download');
        Route::post('/remove-file', 'FileController@remove_file')->name('remove-file');
    }
);
