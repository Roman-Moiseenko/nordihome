<?php

namespace App\Listeners;

use App\Events\ExpenseHasAssembly;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationExpenseAssembly
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
    public function handle(ExpenseHasAssembly $event): void
    {
        //TODO Уведомляем склад, что сборка товара на выдачу или отгрузку
    }
}
