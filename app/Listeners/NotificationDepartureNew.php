<?php

namespace App\Listeners;

use App\Events\DepartureHasCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationDepartureNew
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
    public function handle(DepartureHasCompleted $event): void
    {
        //TODO Уведомление специалистам
    }
}
