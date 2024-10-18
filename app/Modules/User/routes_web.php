<?php
//Cabinet - функции кабинета клиента
use Illuminate\Support\Facades\Route;


//Кабинет
Route::group([
    'as' => 'cabinet.',
    'prefix' => 'cabinet',
    'namespace' => 'Cabinet',
    'middleware' => ['user_cookie_id'],
],
    function () {
        Route::get('/', 'CabinetController@view')->name('view');
        Route::get('/profile', 'CabinetController@profile')->name('profile');
        Route::post('/fullname/{user}', 'CabinetController@fullname')->name('fullname');
        Route::post('/phone/{user}', 'CabinetController@phone')->name('phone');
        Route::post('/email/{user}', 'CabinetController@email')->name('email');
        Route::post('/password/{user}', 'CabinetController@password')->name('password');

        Route::group([
            'as' => 'options.',
            'prefix' => 'options',
        ], function () {
            Route::get('/', 'OptionsController@index')->name('index');
            Route::post('/subscription/{subscription}', 'OptionsController@subscription')->name('subscription');

        });

        Route::group([
            'as' => 'wish.',
            'prefix' => 'wish'
        ], function () {
            Route::get('/', 'WishController@index')->name('index');
            Route::post('/clear', 'WishController@clear')->name('clear');
            Route::post('/get', 'WishController@get')->name('get');
            Route::post('/toggle/{product}', 'WishController@toggle')->name('toggle');
        });

        Route::group([
            'as' => 'order.',
            'prefix' => 'order'
        ], function () {
            Route::get('/', 'OrderController@index')->name('index');
            Route::get('/{order}', 'OrderController@view')->name('view');

        });
        Route::group([
            'as' => 'review.',
            'prefix' => 'review',
        ], function() {
            Route::get('/', 'ReviewController@index')->name('index');
            Route::get('/show/{review}', 'ReviewController@show')->name('show');

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
        Route::post('/login_register', 'LoginController@login_registration')->name('login_register');
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
