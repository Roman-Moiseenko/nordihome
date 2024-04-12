<?php

namespace App\Listeners;

use App\Events\SupplyHasSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationSupplySent
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
    public function handle(SupplyHasSent $event): void
    {
        //TODO Уведомляем службу заказов что заказ отправлен в работу Возможно в доступе добавить Работа с поставщиками
    }
}
