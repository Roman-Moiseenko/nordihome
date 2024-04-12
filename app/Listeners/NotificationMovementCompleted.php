<?php

namespace App\Listeners;

use App\Events\MovementHasCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationMovementCompleted
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
    public function handle(MovementHasCompleted $event): void
    {
        //TODO Перемещение применено !!!
        // Уведомления
    }
}
