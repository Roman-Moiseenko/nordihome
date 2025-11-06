<?php

use App\Modules\Delivery\Controllers\CalendarController;
use App\Modules\Delivery\Controllers\DeliveryController;
use App\Modules\Delivery\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'delivery',
        'as' => 'delivery.',
    ],
    function () {
       // Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
        //Route::get('/calendar/schedule', [CalendarController::class, 'schedule'])->name('calendar.schedule');
        Route::post('/calendar/get-day', [CalendarController::class, 'get_day'])->name('calendar.get-day');

        // Route::post('/assembling/{expense}', 'DeliveryController@assembling')->name('assembling');
        //Смена статуса (вручную)
        Route::post('/assembled/{expense}', [DeliveryController::class, 'assembled'])->name('assembled');//Собран
        Route::post('/completed/{expense}', [DeliveryController::class, 'completed'])->name('completed');//Выдан

        Route::post('/set-cargo/{expense}', [DeliveryController::class, 'set_cargo'])->name('set-cargo'); //Назначить трек-номер и ТК
        Route::post('/delivery/{expense}', [DeliveryController::class, 'delivery'])->name('delivery');
        Route::post('/set-period/{expense}', [DeliveryController::class, 'set_period'])->name('set-period');

        Route::group(
            [
                'prefix' => 'truck',
                'as' => 'truck.',
            ],function () {
            Route::post('/toggle/{truck}', [TruckController::class, 'toggle'])->name('toggle');
            Route::post('/set-info/{truck}', [TruckController::class, 'set_info'])->name('set-info');

        });
        Route::resource('truck', 'TruckController')->only(['index', 'store', 'destroy']);



        //Назначить рабочего
        Route::post('/set-loader/{expense}', [DeliveryController::class, 'set_loader'])->name('set-loader');
        Route::post('/del-loader/{expense}', [DeliveryController::class, 'del_loader'])->name('del-loader');
        Route::post('/set-driver/{expense}', [DeliveryController::class, 'set_driver'])->name('set-driver');
        Route::post('/del-driver/{expense}', [DeliveryController::class, 'del_driver'])->name('del-driver');
        Route::post('/set-assemble/{expense}', [DeliveryController::class, 'set_assemble'])->name('set-assemble');
        Route::post('/del-assemble/{expense}', [DeliveryController::class, 'del_assemble'])->name('del-assemble');


        //Отправить, сменить состояние
        Route::post('/set-complete/{expense}', [DeliveryController::class, 'set_complete'])->name('set-complete');
        //Route::get('/assembly', 'DeliveryController@assembly')->name('assembly'); //На сборку (to-loader)

        //Распоряжения
        Route::get('/', [DeliveryController::class, 'all'])->name('all'); //Все
        Route::get('/to-delivery', [DeliveryController::class, 'to_delivery'])->name('to-delivery'); //На отгрузку
        Route::get('/to-loader', [DeliveryController::class, 'to_loader'])->name('to-loader'); //На упаковку, перед отгрузкой
        //Действия
    }
);
