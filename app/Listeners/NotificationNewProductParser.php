<?php

namespace App\Listeners;

use App\Events\ProductHasParsed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationNewProductParser
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
    public function handle(ProductHasParsed $event): void
    {
        //TODO уведомление сотрудникам что новый товар
    }
}
