<?php

namespace App\Listeners;

use App\Events\CouponHasCreated;
use App\Mail\UserCoupon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationCouponCreated
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
    public function handle(CouponHasCreated $event): void
    {
        Mail::to($event->coupon->user->email)->queue(new UserCoupon($event->coupon));
    }
}
