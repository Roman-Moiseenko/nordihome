<?php

namespace App\Listeners;

use App\Events\OrderHasCanceled;
use App\Mail\OrderCanceled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
