<?php

use App\Modules\Guide\Controllers\AdditionController;
use App\Modules\Guide\Controllers\CargoCompanyController;
use App\Modules\Guide\Controllers\CountryController;
use App\Modules\Guide\Controllers\GuideController;
use App\Modules\Guide\Controllers\MarkingTypeController;
use App\Modules\Guide\Controllers\MeasuringController;
use App\Modules\Guide\Controllers\VATController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'guide',
        'as' => 'guide.',
    ], function () {

    Route::get('/', [GuideController::class, 'index'])->name('index');

    //ADDITION
    Route::group([
        'prefix' => 'addition',
        'as' => 'addition.',
    ], function () {
        Route::get('/', [AdditionController::class, 'index'])->name('index');
        Route::post('/', [AdditionController::class, 'store'])->name('store');
        Route::put('/{addition}', [AdditionController::class, 'update'])->name('update');
        Route::delete('/{addition}', [AdditionController::class, 'destroy'])->name('destroy');
    });

    //COUNTRY
    Route::group([
        'prefix' => 'country',
        'as' => 'country.',
    ], function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
        Route::post('/', [CountryController::class, 'store'])->name('store');
        Route::put('/{country}', [CountryController::class, 'update'])->name('update');
        Route::delete('/{country}', [CountryController::class, 'destroy'])->name('destroy');
    });
    //MARKINGTYPE
    Route::group([
        'prefix' => 'marking-type',
        'as' => 'marking-type.',
    ], function () {
        Route::get('/', [MarkingTypeController::class, 'index'])->name('index');
        Route::post('/', [MarkingTypeController::class, 'store'])->name('store');
        Route::put('/{marking_type}', [MarkingTypeController::class, 'update'])->name('update');
        Route::delete('/{marking_type}', [MarkingTypeController::class, 'destroy'])->name('destroy');
    });
    //MEASURING
    Route::group([
        'prefix' => 'measuring',
        'as' => 'measuring.',
    ], function () {
        Route::get('/', [MeasuringController::class, 'index'])->name('index');
        Route::post('/', [MeasuringController::class, 'store'])->name('store');
        Route::put('/{measuring}', [MeasuringController::class, 'update'])->name('update');
        Route::delete('/{measuring}', [MeasuringController::class, 'destroy'])->name('destroy');
    });
    //VAT
    Route::group([
        'prefix' => 'vat',
        'as' => 'vat.',
    ], function () {
        Route::get('/', [VATController::class, 'index'])->name('index');
        Route::post('/', [VATController::class, 'store'])->name('store');
        Route::put('/{vat}', [VATController::class, 'update'])->name('update');
        Route::delete('/{vat}', [VATController::class, 'destroy'])->name('destroy');
    });

    //ADDITION
    Route::group([
        'prefix' => 'cargo-company',
        'as' => 'cargo-company.',
    ], function () {
        Route::get('/', [CargoCompanyController::class, 'index'])->name('index');
        Route::post('/', [CargoCompanyController::class, 'store'])->name('store');
        Route::put('/{cargo}', [CargoCompanyController::class, 'update'])->name('update');
        Route::delete('/{cargo}', [CargoCompanyController::class, 'destroy'])->name('destroy');
    });
});
