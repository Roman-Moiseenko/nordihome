<?php

namespace App\Listeners;

use App\Events\SupplyHasCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationSupplyCompleted
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
    public function handle(SupplyHasCompleted $event): void
    {
        //TODO Уведомления тем, кто принимает заказ. На склады где созданы поступления ???
    }
}
