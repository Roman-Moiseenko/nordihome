<?php

use App\Modules\Lead\Presentation\Http\Controllers\Web\LeadController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin',
    'as' => 'admin.',
],function () {
    //Маршруты тут
});
Route::group(
    [
        'middleware' => 'role:admin|staff',
        'prefix' => 'admin/lead',
        'as' => 'admin.lead.',
    ], function () {

    Route::get('/', [LeadController::class, 'index'])->name('index');

    Route::post('/in-work/{id}', [LeadController::class, 'setInWork'])->name('in-work');
    Route::post('/not-decided/{id}', [LeadController::class, 'setNotDecided'])->name('not-decided');
    Route::post('/return-new/{id}', [LeadController::class, 'setReturnNew'])->name('return-new');
    Route::get('/leads', [LeadController::class, 'getLeads'])->name('leads');


    Route::post('/add-comment/{id}', [LeadController::class, 'addComment'])->name('add-comment');
    Route::post('/set-name/{id}', [LeadController::class, 'setName'])->name('set-name');
    Route::post('/canceled/{lead}', [LeadController::class, 'canceled'])->name('canceled');
    //Route::post('/completed/{lead}', [LeadController::class, 'completed'])->name('completed');

    Route::post('/create-client/{id}', [LeadController::class, 'createClient'])->name('create-client');
    Route::post('/create-order/{id}', [LeadController::class, 'createOrder'])->name('create-order');


});
