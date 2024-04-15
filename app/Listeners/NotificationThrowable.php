<?php

namespace App\Listeners;

use App\Events\ThrowableHasAppeared;
use App\Mail\AdminThrowable;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Notifications\StaffMessage;
use Illuminate\Support\Facades\Mail;

class NotificationThrowable
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
    public function handle(ThrowableHasAppeared $event): void
    {
        $staffs = Admin::where('role', Admin::ROLE_ADMIN)->orWhereHas('responsibilities', function ($q) {
            $q->where('code', Responsibility::REPORT_THROWABLE);
        })->get();


        $message = "Ошибка на сайте:\n" . $event->throwable->getMessage() . "\n" . $event->throwable->getFile() . "\n" . $event->throwable->getLine();

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
            Mail::to($staff->email)->send(new AdminThrowable($event->throwable));
        }
    }
}
