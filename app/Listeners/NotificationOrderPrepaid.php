<?php

namespace App\Listeners;

use App\Events\OrderHasPrepaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationOrderPrepaid
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
    public function handle(OrderHasPrepaid $event): void
    {
        //$event->order
        //TODO Уведомляем менеджера и клиента, что его оплата внесена
    }
}
