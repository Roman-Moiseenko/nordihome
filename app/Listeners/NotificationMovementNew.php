<?php

namespace App\Listeners;

use App\Events\MovementHasCreated;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationMovementNew
{
    private StaffRepository $repository;

    public function __construct(StaffRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(MovementHasCreated $event): void
    {
        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_ACCOUNTING);
        //TODO
        $text = '';/*
        foreach ($event->documents as $document) {
            $text .= "\n со склада " . $document->storageOut->name . " \n на склад " . $document->storageIn->name;
        }
*/
        $message = "Новое перемещение" . $text;
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
        }
    }
}
