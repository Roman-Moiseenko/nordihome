<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'feedback',
    'as' => 'feedback.',
    //'namespace' => 'Feedback',
], function() {
    Route::get('/review', 'ReviewController@index')->name('review.index');
    Route::get('/review/{review}', 'ReviewController@show')->name('review.show');
    Route::post('/review/{review}/published', 'ReviewController@published')->name('review.published');
    Route::post('/review/{review}/blocked', 'ReviewController@blocked')->name('review.blocked');
});
