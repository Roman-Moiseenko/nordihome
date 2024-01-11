<?php

namespace App\Listeners;

use App\Events\UserHasRegistered;
use App\Mail\UserRegister;
use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Coupon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class WelcomToShop
{
    private Options $options;

    /**
     * Create the event listener.
     */
    public function __construct(Options $options)
    {
        //
        $this->options = $options;
    }

    /**
     * Handle the event.
     */
    public function handle(UserHasRegistered $event): void
    {
        //TODO Из опций получаем время и бонус для клиента

        $coupon = Coupon::register($event->user->id, 500, now(), now()->addHours(3));
        Mail::to($event->user->email)->queue(new UserRegister($event->user, $coupon));
    }
}
