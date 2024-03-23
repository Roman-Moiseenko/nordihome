<?php

namespace App\Listeners;

use App\Events\UserHasCreated;
use App\Mail\VerifyLinkMail;
use App\Mail\VerifyMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
        Mail::to($event->user->email)->send(new VerifyLinkMail($event->user));
    }
}
