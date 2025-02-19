<?php

namespace App\Listeners;

use App\Events\ThrowableHasAppeared;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\AdminThrowable;
use App\Notifications\StaffMessage;

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


        $message = $event->throwable->getMessage() . "\n" . $event->throwable->getFile() . "\n" . $event->throwable->getLine();

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage('Ошибка на сайте:', $message));
            //TODO
            SendSystemMail::dispatch($staff, new AdminThrowable($event->throwable));
        }
    }
}
