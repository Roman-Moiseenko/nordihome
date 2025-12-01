<?php

namespace App\Listeners;

use App\Events\UserHasCreated;

use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\VerifyMail;
use Illuminate\Support\Facades\Mail;

class NotificationUserCreated
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
    public function handle(UserHasCreated $event): void
    {

        SendSystemMail::dispatch($event->user, new VerifyMail($event->user), null, null);


        //Mail::to($event->user->email)->send(new VerifyMail($event->user));
    }
}
