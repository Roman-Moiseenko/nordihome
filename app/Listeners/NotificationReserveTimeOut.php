<?php

namespace App\Listeners;

use App\Events\ReserveHasTimeOut;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationReserveTimeOut
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReserveHasTimeOut $event): void
    {
        if ($event->timeOut) {
            //TODO Сообщение менеджеру что резерв закончился или заканчивается
            // Проверки, если заказ не оплачен или частично, то письмо клиенту
        } else {
            //TODO Сообщаем, что через 12 часов закончится, пора оплатить или что-то предпринять
            // Время вычисляем
        }

    }
}
