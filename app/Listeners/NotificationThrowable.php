<?php

namespace App\Listeners;

use App\Events\ThrowableHasAppeared;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationThrowable
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(ThrowableHasAppeared $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::administrator());


        $message = $event->throwable->getMessage() . "\n" . $event->throwable->getFile() . "\n" . $event->throwable->getLine();
        //FIXME Модуль Notification - через RecipientResolverInterface
      /*
       *   foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage('Ошибка на сайте:', $message));
            SendSystemMail::dispatch($staff, new AdminThrowable($event->throwable));
        }

        */
    }
}
