<?php

namespace App\Listeners;

use App\Entity\Admin;
use App\Events\ThrowableHasAppeared;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $staffs = Admin::where('role', Admin::ROLE_ADMIN)->get();
        $message = "Ошибка на сайте:\n" . $event->throwable->getMessage() . '\n' . $event->throwable->getFile() . '\n' . $event->throwable->getLine();
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
            //TODO Добавить отправку письма С текстом ошибки и трассировкой
            //$staff->notify(new StaffEmail($message));
        }
    }
}
