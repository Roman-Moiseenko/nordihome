<?php


use App\Modules\User\Controllers\CartController;
use App\Modules\User\Controllers\SubscriptionController;
use App\Modules\User\Controllers\UserController;
use App\Modules\User\Controllers\WishController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
], function(){

    Route::post('/user-params', [UserController::class, 'user_params'])->name('user-params');
    Route::post('/attach/{user}', [UserController::class, 'attach'])->name('attach');
    Route::post('/detach/{user}', [UserController::class, 'detach'])->name('detach');
    Route::post('/default/{user}', [UserController::class, 'default'])->name('default');
    Route::post('/set-info/{user}', [UserController::class, 'set_info'])->name('set-info');
    Route::post('/verify/{user}', [UserController::class, 'verify'])->name('verify');
    Route::post('/upload/{user}', [UserController::class, 'upload'])->name('upload');
    Route::post('/search', [UserController::class, 'search'])->name('search');
    Route::post('/get-edit-data', [UserController::class, 'get_edit_data'])->name('get-edit-data');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/wish', [WishController::class, 'index'])->name('wish.index');
    Route::resource('subscription', 'SubscriptionController')->only(['index', 'show']); //CRUD

    //Subscription
    Route::group(
        [
            'prefix' => 'subscription',
            'as' => 'subscription.',
        ],
        function () {
            Route::post('/activated/{subscription}', [SubscriptionController::class, 'activated'])->name('activated');
            Route::post('/draft/{subscription}', [SubscriptionController::class, 'draft'])->name('draft');
            Route::post('/set-info/{subscription}', [SubscriptionController::class, 'set_info'])->name('set-info');
        }
    );

    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::post('/create', [UserController::class, 'create'])->name('create');
    Route::get('/', [UserController::class, 'index'])->name('index');

});

