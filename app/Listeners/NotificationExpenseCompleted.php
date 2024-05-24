<?php

namespace App\Listeners;

use App\Events\ExpenseHasCompleted;
use App\Jobs\RequestReview;
use App\Mail\ExpenseCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationExpenseCompleted
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
        Mail::to($event->expense->order->user->email)->queue(new ExpenseCompleted($event->expense));
    }
}
