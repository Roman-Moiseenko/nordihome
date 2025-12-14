<?php

namespace App\Listeners;

use App\Mail\OrderCanceled;
use App\Modules\Order\Events\OrderHasCanceled;
use Illuminate\Support\Facades\Mail;

class NotificationOrderCanceled
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
    public function handle(OrderHasCanceled $event): void
    {
        //Письмо клиенту, что заказ отменен
        Mail::to($event->order->user->email)->queue(new OrderCanceled($event->order));
    }
}
