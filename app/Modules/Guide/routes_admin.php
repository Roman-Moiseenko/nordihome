<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'guide',
        'as' => 'guide.',
    ], function () {

    Route::get('/', 'GuideController@index')->name('index');

    //ADDITION
    Route::group([
        'prefix' => 'addition',
        'as' => 'addition.',
    ], function () {
        Route::get('/', 'AdditionController@index')->name('index');
        Route::post('/', 'AdditionController@store')->name('store');
        Route::put('/{addition}', 'AdditionController@update')->name('update');
        Route::delete('/{addition}', 'AdditionController@destroy')->name('destroy');
    });

    //COUNTRY
    Route::group([
        'prefix' => 'country',
        'as' => 'country.',
    ], function () {
        Route::get('/', 'CountryController@index')->name('index');
        Route::post('/', 'CountryController@store')->name('store');
        Route::put('/{country}', 'CountryController@update')->name('update');
        Route::delete('/{country}', 'CountryController@destroy')->name('destroy');
    });
    //MARKINGTYPE
    Route::group([
        'prefix' => 'marking-type',
        'as' => 'marking-type.',
    ], function () {
        Route::get('/', 'MarkingTypeController@index')->name('index');
        Route::post('/', 'MarkingTypeController@store')->name('store');
        Route::put('/{marking_type}', 'MarkingTypeController@update')->name('update');
        Route::delete('/{marking_type}', 'MarkingTypeController@destroy')->name('destroy');
    });
    //MEASURING
    Route::group([
        'prefix' => 'measuring',
        'as' => 'measuring.',
    ], function () {
        Route::get('/', 'MeasuringController@index')->name('index');
        Route::post('/', 'MeasuringController@store')->name('store');
        Route::put('/{measuring}', 'MeasuringController@update')->name('update');
        Route::delete('/{measuring}', 'MeasuringController@destroy')->name('destroy');
    });
    //VAT
    Route::group([
        'prefix' => 'vat',
        'as' => 'vat.',
    ], function () {
        Route::get('/', 'VATController@index')->name('index');
        Route::post('/', 'VATController@store')->name('store');
        Route::put('/{vat}', 'VATController@update')->name('update');
        Route::delete('/{vat}', 'VATController@destroy')->name('destroy');
    });

    //ADDITION
    Route::group([
        'prefix' => 'cargo-company',
        'as' => 'cargo-company.',
    ], function () {
        Route::get('/', 'CargoCompanyController@index')->name('index');
        Route::post('/', 'CargoCompanyController@store')->name('store');
        Route::put('/{cargo}', 'CargoCompanyController@update')->name('update');
        Route::delete('/{cargo}', 'CargoCompanyController@destroy')->name('destroy');
    });
});
