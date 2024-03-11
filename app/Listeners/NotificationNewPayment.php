<?php

namespace App\Listeners;

use App\Events\PaymentHasPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationNewPayment
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
    public function handle(PaymentHasPaid $event): void
    {
        //TODO Для автоматических платежей - Уведомляем менеджера, что платеж получен и уведомляем клиента
    }
}
