<?php

namespace App\Listeners;

use App\Events\OrderHasCompleted;
use App\Jobs\RequestReview;
use App\Mail\OrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationOrderCompleted
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
    public function handle(OrderHasCompleted $event): void
    {

        Mail::to($event->order->user->email)->queue(new OrderCompleted($event->order));
        //Отправляем в очередь для запроса отзывов на купленный товар и начисления бонусов
        RequestReview::dispatch($event->order)->delay(now()->addDays(3));
    }
}
