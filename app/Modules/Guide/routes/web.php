<?php

use App\Modules\Guide\Controllers\CargoCompanyController;
use App\Modules\Guide\Controllers\CountryController;
use App\Modules\Guide\Controllers\GuideController;
use App\Modules\Guide\Controllers\MarkingTypeController;
use App\Modules\Guide\Controllers\MeasuringController;
use App\Modules\Guide\Controllers\VATController;
use App\Modules\Guide\Presentation\Http\Controllers\Web\AdditionController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/guide',
    'as' => 'admin.guide.',
],function () {
    Route::get('/', [GuideController::class, 'index'])->name('index');

    //ADDITION
    Route::group([
        'prefix' => 'addition',
        'as' => 'addition.',
    ], function () {
        Route::get('/', [AdditionController::class, 'index'])->name('index');
        Route::get('/list', [AdditionController::class, 'groupList'])->name('list');
        Route::post('/', [AdditionController::class, 'store'])->name('store');
        Route::put('/{id}', [AdditionController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdditionController::class, 'destroy'])->name('destroy');
    });

    //COUNTRY
    Route::group([
        'prefix' => 'country',
        'as' => 'country.',
    ], function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
        Route::get('/list', [CountryController::class, 'list'])->name('list');
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
        Route::get('/list', [MarkingTypeController::class, 'list'])->name('list');
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
        Route::get('/list', [MeasuringController::class, 'list'])->name('list');
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
        Route::get('/list', [VATController::class, 'list'])->name('list');
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
