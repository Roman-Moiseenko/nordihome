<?php

namespace App\Listeners;

use App\Events\ExpenseHasDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationExpenseDelivery
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
    public function handle(ExpenseHasDelivery $event): void
    {
        //TODO Уведомляем клиента, что его заказ отправлен и ему присвоен трек-номер
    }
}
