<?php

namespace App\Listeners;


use App\Modules\Order\Events\OrderHasCompleted;


class NotificationOrderCompleted
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasCompleted $event): void
    {



    }
}
