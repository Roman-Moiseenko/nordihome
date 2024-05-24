<?php

namespace App\Listeners;

use App\Events\ExpenseHasDelivery;
use App\Mail\ExpenseDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationExpenseDelivery
{

    public function __construct()
    {
    }

    public function handle(ExpenseHasDelivery $event): void
    {
        if ($event->expense->isRegion()) Mail::to($event->expense->order->user->email)->queue(new ExpenseDelivery($event->expense));
    }
}
