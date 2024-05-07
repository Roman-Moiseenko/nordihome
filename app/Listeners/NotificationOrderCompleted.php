<?php

namespace App\Listeners;

use App\Events\OrderHasCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationOrderCompleted
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
    public function handle(OrderHasCompleted $event): void
    {
        //TODO Пишем письмо клиенту, что его заказ полностью завершен отправляем бонус или что то еще
        // $event->order;
    }
}
