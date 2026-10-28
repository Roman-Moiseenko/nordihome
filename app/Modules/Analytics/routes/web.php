<?php

use App\Modules\Analytics\Presentation\Http\Controllers\Web\AnalyticsController;
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
], function () {
    Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
    Route::get('/searches', [AnalyticsController::class, 'searches'])->name('searches');
    Route::get('/pages', [AnalyticsController::class, 'pages'])->name('pages');
    Route::get('/actions', [AnalyticsController::class, 'actions'])->name('actions');
    Route::get('/sources', [AnalyticsController::class, 'sources'])->name('sources');
    Route::get('/audience', [AnalyticsController::class, 'audience'])->name('audience');
    Route::get('/funnel', [AnalyticsController::class, 'funnel'])->name('funnel');
});
