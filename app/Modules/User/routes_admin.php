<?php


use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
], function(){

    Route::post('/attach/{user}', 'UserController@attach')->name('attach');
    Route::post('/detach/{user}', 'UserController@detach')->name('detach');
    Route::post('/default/{user}', 'UserController@default')->name('default');
    Route::post('/set-info/{user}', 'UserController@set_info')->name('set-info');
    Route::post('/verify/{user}', 'UserController@verify')->name('verify');

    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::get('/wish', 'WishController@index')->name('wish.index');

    //Subscription
    Route::group(
        [
            'prefix' => 'subscription',
            'as' => 'subscription.',
            //'namespace' => '',
        ],
        function () {
            Route::post('/published/{subscription}', 'SubscriptionController@published')->name('published');
            Route::post('/draft/{subscription}', 'SubscriptionController@draft')->name('draft');
        }
    );


    Route::get('/{user}', 'UserController@show')->name('show');
    Route::post('/create', 'UserController@create')->name('create');
    Route::get('/', 'UserController@index')->name('index');

    Route::resource('subscription', 'SubscriptionController')->except(['create', 'store', 'destroy']); //CRUD
});

