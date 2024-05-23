<?php

namespace App\Listeners;

use App\Events\OrderHasLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationNewLogger
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
    public function handle(OrderHasLogger $event): void
    {
        //TODO Уведомляем сборщика, что надо собрать заказ
        // На будущее, только Телеграм .....
    }
}
