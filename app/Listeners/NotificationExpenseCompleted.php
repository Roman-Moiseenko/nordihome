<?php

namespace App\Listeners;

use App\Events\ExpenseHasCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        //TODO Пишем письмо клиенту, что его заявка на выдачу исполнена полностью
        // $event->expense;
    }
}
