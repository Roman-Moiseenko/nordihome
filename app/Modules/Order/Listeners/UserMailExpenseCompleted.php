<?php

namespace App\Modules\Order\Listeners;

use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\ExpenseCompleted;
use App\Modules\Order\Events\ExpenseHasCompleted;
use App\Modules\Order\Infrastructure\Models\Order;

class UserMailExpenseCompleted
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
    public function handle(ExpenseHasCompleted $event): void
    {
        SendSystemMail::dispatch(
            $event->expense->order->client,
            new ExpenseCompleted($event->expense),
            Order::class,
            $event->expense->order->id
        );

    }
}
