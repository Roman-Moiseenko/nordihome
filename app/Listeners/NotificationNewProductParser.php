<?php

namespace App\Listeners;

use App\Entity\Admin;
use App\Events\ProductHasParsed;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use NotificationChannels\Telegram\TelegramUpdates;

class NotificationNewProductParser
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
        //TODO уведомление сотрудникам что новый товар
        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_PRODUCT);

        $message = "Добавлен новый товар через Парсер\n Артикул товара " . $event->product->code;
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage($message));
        }
    }
}
