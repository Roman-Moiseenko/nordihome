<?php

use App\Modules\Analytics\Presentation\Http\Controllers\Web\AnalyticsEventController;
use Illuminate\Support\Facades\Route;

// Публичные endpoint'ы для событий из JS (без аутентификации, исключены из CSRF).
Route::post('/analytics/track-action', [AnalyticsEventController::class, 'trackAction'])
    ->name('analytics.track-action');

Route::post('/analytics/record-exit', [AnalyticsEventController::class, 'recordExit'])
    ->name('analytics.record-exit');

Route::post('/analytics/track-search-click', [AnalyticsEventController::class, 'trackSearchClick'])
    ->name('analytics.track-search-click');

Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin/analytics',
    'as' => 'admin.analytics.',
],function () {
    //Маршруты тут
});
