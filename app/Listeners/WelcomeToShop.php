<?php

namespace App\Listeners;

use App\Events\UserHasRegistered;


use App\Modules\Discount\Entity\Coupon;
use App\Modules\Mail\Job\SendSystemMail;

use App\Modules\Mail\Mailable\UserRegister;
use Illuminate\Support\Facades\Mail;

class WelcomeToShop
{

    public function __construct()
    {

    }

    public function handle(UserHasRegistered $event): void
    {
        //TODO Купон при регистрации

        $coupon = Coupon::register(
            $event->user->id,
            500,
            now(),
            now()->addDays(30));
        SendSystemMail::dispatch($event->user, new UserRegister($event->user, $coupon), null, null);
    }
}
