<?php

namespace App\Listeners;

use App\Events\UserHasRegistered;
use App\Mail\UserRegister;
use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Coupon;
use Illuminate\Support\Facades\Mail;

class WelcomToShop
{
    private Options $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function handle(UserHasRegistered $event): void
    {
        $coupon = Coupon::register(
            $event->user->id,
            $this->options->shop->coupon_first_bonus,
            now(),
            now()->addDays($this->options->shop->coupon_first_time));
        Mail::to($event->user->email)->queue(new UserRegister($event->user, $coupon));
    }
}
