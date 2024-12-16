<?php


use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'staff',
    'as' => 'staff.',
], function () {
    Route::get('/notification', 'StaffController@notification')->name('notification');
    Route::post('/notification-read/{notification}', 'StaffController@notification_read')->name('notification-read');

    Route::get('/security/{staff}', 'StaffController@security')->name('security');
    Route::post('/password/{staff}', 'StaffController@password')->name('password');
    Route::post('/activate/{staff}', 'StaffController@activate')->name('activate');
    Route::post('/photo/{staff}', 'StaffController@setPhoto')->name('photo');
    Route::post('/response/{staff}', 'StaffController@response')->name('response');
});
Route::group([
    'prefix' => 'worker',
    'as' => 'worker.',
], function () {
    Route::post('/toggle/{worker}', 'WorkerController@toggle')->name('toggle');
    Route::post('/update/{worker}', 'WorkerController@update')->name('update');
});

Route::resource('staff', 'StaffController'); //CRUD
Route::resource('worker', 'WorkerController')->except(['create', 'edit', 'update']); //CRUD

//Настройки
Route::group(
    [
        'prefix' => 'settings',
        'as' => 'settings.',
        //'namespace' => 'Settings',
    ],
    function () {
        Route::get('/shop', 'ShopSettingsController@index')->name('shop');
        Route::post('/shop', 'ShopSettingsController@update');
    }
);

Route::get('/', 'HomeController@index')->name('home');
