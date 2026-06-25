<?php

namespace App\Listeners;

use App\Events\DepartureHasCompleted;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;


class NotificationDepartureNew
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    public function handle(DepartureHasCompleted $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::supervisor());
                //FIXME Модуль Notification - через RecipientResolverInterface
      /*

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Списание товаров',
                $event->document->htmlNumDate(),
                route('admin.accounting.departure.show', $event->document),
                'folder-output'
            ));
        }
*/
    }
}
