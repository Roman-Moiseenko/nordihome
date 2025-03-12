<?php

namespace App\Listeners;

use App\Events\ExpenseHasDelivery;

use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\ExpenseDelivery;
use App\Modules\Order\Entity\Order\Order;

class NotificationExpenseDelivery
{

    public function __construct()
    {
    }

    public function handle(ExpenseHasDelivery $event): void
    {
        if ($event->expense->isRegion())
            SendSystemMail::dispatch(
                $event->expense->order->user,
                new ExpenseDelivery($event->expense),
                Order::class,
                $event->expense->order->id
            );
    }
}
