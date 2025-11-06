<?php

use App\Modules\Notification\Controllers\NotificationController;
use App\Modules\Notification\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'notification',
    'as' => 'notification.',
],
function(){
    Route::post('/notification/read/{notification}', [NotificationController::class, 'read'])->name('notification.read');
    Route::post('/telegram/chat-id', [TelegramController::class, 'chat_id'])->name('telegram.chat-id');
    Route::Resource('notification', 'NotificationController')->only(['index', 'create', 'store']);
});

