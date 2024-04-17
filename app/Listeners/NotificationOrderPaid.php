<?php

namespace App\Listeners;

use App\Events\OrderHasPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationOrderPaid
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
    public function handle(OrderHasPaid $event): void
    {
        //TODO Уведомляем менеджера и клиента, что его заказ полностью оплачен
    }
}
