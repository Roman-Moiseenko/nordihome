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
    Route::post('/upload/{user}', 'UserController@upload')->name('upload');
    Route::post('/search-add', 'UserController@search_add')->name('search-add');

    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::get('/wish', 'WishController@index')->name('wish.index');
    Route::resource('subscription', 'SubscriptionController')->only(['index', 'show']); //CRUD
    //Subscription
    Route::group(
        [
            'prefix' => 'subscription',
            'as' => 'subscription.',
            //'namespace' => '',
        ],
        function () {
            Route::post('/activated/{subscription}', 'SubscriptionController@activated')->name('activated');
            Route::post('/draft/{subscription}', 'SubscriptionController@draft')->name('draft');
            Route::post('/set-info/{subscription}', 'SubscriptionController@set_info')->name('set-info');
        }
    );

    Route::get('/{user}', 'UserController@show')->name('show');
    Route::post('/create', 'UserController@create')->name('create');
    Route::get('/', 'UserController@index')->name('index');

});

