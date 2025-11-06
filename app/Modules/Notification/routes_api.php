<?php
declare(strict_types=1);


use App\Modules\Notification\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;


Route::any('/telegram/web-hook', [TelegramController::class, 'web_hook'])->name('telegram.web-hook');

