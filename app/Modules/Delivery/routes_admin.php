<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'delivery',
        'as' => 'delivery.',
        //'namespace' => 'Delivery',
    ],
    function () {
        //Просмотры - index
        Route::get('/', 'DeliveryController@index')->name('all');
        Route::get('/local', 'DeliveryController@index_local')->name('local');
        Route::get('/region', 'DeliveryController@index_region')->name('region');
        Route::get('/storage', 'DeliveryController@index_storage')->name('storage');
        Route::get('/assembly', 'DeliveryController@assembly')->name('assembly');

        Route::get('/calendar', 'CalendarController@index')->name('calendar.index');
        Route::get('/calendar/schedule', 'CalendarController@schedule')->name('calendar.schedule');
        Route::post('/calendar/get-day', 'CalendarController@get_day')->name('calendar.get-day');

        Route::post('/assembling/{expense}', 'DeliveryController@assembling')->name('assembling');

        Route::post('/delivery/{expense}', 'DeliveryController@delivery')->name('delivery');
        Route::post('/completed/{expense}', 'DeliveryController@completed')->name('completed');

        Route::resource('truck', 'TruckController');
        Route::post('/truck/toggle/{truck}', 'TruckController@toggle')->name('truck.toggle');
        //Действия
    }
);
