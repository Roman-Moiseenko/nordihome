<?php
//Cabinet - функции кабинета клиента
use App\Modules\User\Controllers\Auth\LoginController;
use App\Modules\User\Controllers\Cabinet\CabinetController;
use App\Modules\User\Controllers\Cabinet\OptionsController;
use App\Modules\User\Controllers\Cabinet\OrderController;
use App\Modules\User\Controllers\Cabinet\ReviewController;
use App\Modules\User\Controllers\Cabinet\WishController;
use Illuminate\Support\Facades\Route;


//Кабинет
Route::group([
    'as' => 'cabinet.',
    'prefix' => 'cabinet',
    'namespace' => 'Cabinet',
    'middleware' => ['user_cookie_id'],
],
    function () {
        Route::get('/', [CabinetController::class, 'view'])->name('view');
        Route::get('/profile', [CabinetController::class, 'profile'])->name('profile');
        Route::post('/fullname/{user}', [CabinetController::class, 'fullname'])->name('fullname');
        Route::post('/phone/{user}', [CabinetController::class, 'phone'])->name('phone');
        Route::post('/email/{user}', [CabinetController::class, 'email'])->name('email');
        Route::post('/password/{user}', [CabinetController::class, 'password'])->name('password');

        Route::group([
            'as' => 'options.',
            'prefix' => 'options',
        ], function () {
            Route::get('/', [OptionsController::class, 'index'])->name('index');
            Route::post('/subscription/{subscription}', [OptionsController::class, 'subscription'])->name('subscription');

        });

        Route::group([
            'as' => 'wish.',
            'prefix' => 'wish'
        ], function () {
            Route::get('/', [WishController::class, 'index'])->name('index');
            Route::post('/clear', [WishController::class, 'clear'])->name('clear');
            Route::post('/get', [WishController::class, 'get'])->name('get');
            Route::post('/toggle/{product}', [WishController::class, 'toggle'])->name('toggle');
        });

        Route::group([
            'as' => 'order.',
            'prefix' => 'order'
        ], function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'view'])->name('view');

        });
        Route::group([
            'as' => 'review.',
            'prefix' => 'review',
        ], function() {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/show/{review}', [ReviewController::class, 'show'])->name('show');

        });
    }
);

//Аутентификация
Route::group(
    [
        'namespace' => 'Auth',
        'middleware' => ['user_cookie_id'],
    ],
    function () {
        //Route::get('/login', 'LoginController@showLoginForm')->name('login');
        Route::post('/login', 'LoginController@login')->name('login');
        Route::get('/login', function () {
            abort(404);
        });
        Route::any('/login_register', [LoginController::class, 'login_registration'])->name('login_register');
        Route::any('/logout', 'LoginController@logout')->name('logout');

        Route::get('/register/verify', 'RegisterController@verify')->name('register.verify');
        Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('/register', 'RegisterController@register');


        Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.update');
        Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
        Route::any('/password/request', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::get('/password/confirm', 'ConfirmPasswordController@showConfirmForm')->name('password.confirm');
        Route::post('/password/confirm', 'ConfirmPasswordController@confirm');
        Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    }
);
