<?php

namespace App\Listeners;

use App\Events\PriceHasMinimum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationPriceMinimum
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
    public function handle(PriceHasMinimum $event): void
    {
        //TODO Заменить на уведомление руководства

        throw new \DomainException('Цена продажи меньше установленной минимальной для товара ' . $event->item->product->name);
    }
}
