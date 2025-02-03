<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'delivery',
        'as' => 'delivery.',
    ],
    function () {
        Route::get('/calendar', 'CalendarController@index')->name('calendar.index');
        Route::get('/calendar/schedule', 'CalendarController@schedule')->name('calendar.schedule');
        Route::post('/calendar/get-day', 'CalendarController@get_day')->name('calendar.get-day');

        // Route::post('/assembling/{expense}', 'DeliveryController@assembling')->name('assembling');
        //Смена статуса (вручную)
        Route::post('/assembled/{expense}', 'DeliveryController@assembled')->name('assembled');//Собран
        Route::post('/completed/{expense}', 'DeliveryController@completed')->name('completed');//Выдан

        Route::post('/set-cargo/{expense}', 'DeliveryController@set_cargo')->name('set-cargo'); //Назначить трек-номер и ТК
        Route::post('/delivery/{expense}', 'DeliveryController@delivery')->name('delivery');
        Route::post('/set-period/{expense}', 'DeliveryController@set_period')->name('set-period');

        Route::resource('truck', 'TruckController');
        Route::post('/truck/toggle/{truck}', 'TruckController@toggle')->name('truck.toggle');

        //Назначить рабочего
        Route::post('/set-loader/{expense}', 'DeliveryController@set_loader')->name('set-loader');
        Route::post('/del-loader/{expense}', 'DeliveryController@del_loader')->name('del-loader');
        Route::post('/set-driver/{expense}', 'DeliveryController@set_driver')->name('set-driver');
        Route::post('/del-driver/{expense}', 'DeliveryController@del_driver')->name('del-driver');
        Route::post('/set-assemble/{expense}', 'DeliveryController@set_assemble')->name('set-assemble');
        Route::post('/del-assemble/{expense}', 'DeliveryController@del_assemble')->name('del-assemble');


        //Отправить, сменить состояние
        //Route::get('/assembly', 'DeliveryController@assembly')->name('assembly'); //На сборку (to-loader)

        //Распоряжения
        Route::get('/', 'DeliveryController@all')->name('all'); //Все
        Route::get('/to-delivery', 'DeliveryController@to_delivery')->name('to-delivery'); //На отгрузку
        Route::get('/to-loader', 'DeliveryController@to_loader')->name('to-loader'); //На упаковку, перед отгрузкой
        //Действия
    }
);
