<?php

namespace App\Listeners;

use App\Jobs\RequestReview;
use App\Mail\OrderCompleted;
use App\Modules\Order\Events\OrderHasCompleted;
use App\Modules\Setting\Entity\Settings;
use Illuminate\Support\Facades\Mail;

class NotificationOrderCompleted
{
    private int $bonus_discount_delay;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $settings = app()->make(Settings::class);
        $coupon = $settings->coupon;
        $this->bonus_discount_delay = $coupon->bonus_discount_delay;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasCompleted $event): void
    {

        Mail::to($event->order->user->email)->queue(new OrderCompleted($event->order));
        //Отправляем в очередь для запроса отзывов на купленный товар и начисления бонусов
        //TODO Переключить на продакшн
        RequestReview::dispatch($event->order)->delay(now()->addMinutes(1));

        return;

        RequestReview::dispatch($event->order)->delay(now()->addDays($this->bonus_discount_delay));

    }
}
