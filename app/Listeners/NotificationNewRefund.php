<?php

namespace App\Listeners;

use App\Events\OrderHasRefund;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationNewRefund
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
    public function handle(OrderHasRefund $event): void
    {
        //TODO Уведомляем кто работает с возратом

    }
}
