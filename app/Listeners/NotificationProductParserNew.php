<?php

namespace App\Listeners;

use App\Events\ProductHasParsed;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;

class NotificationProductParserNew
{
    private StaffRepository $repository;

    /**
     * Create the event listener.
     */
    public function __construct(StaffRepository $repository)
    {
        //
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     */
    public function handle(ProductHasParsed $event): void
    {
        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_PRODUCT);

        $message = "Добавлен новый товар через Парсер\n Артикул товара " . $event->product->code;
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
        }
    }
}
