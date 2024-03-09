<?php

namespace App\Listeners;

use App\Events\PointHasEstablished;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationNewPointStorage
{
    private StaffRepository $repository;

    public function __construct(StaffRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(PointHasEstablished $event): void
    {
        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_LOGGER);

        $message = "Новая сборка товара для заказа\n " . $event->order->htmlNum() . " от " . $event->order->htmlDate();
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
        }
    }
}
