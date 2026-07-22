<?php

// use Illuminate\Support\Facades\Route;

// Route::middleware([])->prefix('notification')->group(function () {

//     Route::get('/api', function () {
//         return 'notification';
//     });

// });


use App\Modules\Notification\Controllers\TelegramController;

Route::any('/telegram/web-hook', [TelegramController::class, 'web_hook'])->name('telegram.web-hook');
