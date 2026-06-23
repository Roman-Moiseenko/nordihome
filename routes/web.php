<?php


use Illuminate\Support\Facades\Route;


Route::any('/api/telegram', [\App\Http\Controllers\Api\TelegramController::class, 'get'])->name('api.telegram');






