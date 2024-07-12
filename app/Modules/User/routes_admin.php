<?php


use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
], function(){
    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::get('/wish', 'WishController@index')->name('wish.index');
    Route::resource('subscription', 'SubscriptionController')->except(['create', 'store', 'destroy']); //CRUD
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
    Route::post('/{user}/verify', 'UsersController@verify')->name('verify');
    Route::get('/{user}', 'UsersController@show')->name('show');


    Route::get('/', 'UsersController@index')->name('index');


});

