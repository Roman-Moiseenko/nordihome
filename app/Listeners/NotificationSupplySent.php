<?php

namespace App\Listeners;

use App\Events\SupplyHasSent;
use App\Modules\Admin\Repository\StaffRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationSupplySent
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(SupplyHasSent $event): void
    {
        //TODO Уведомляем службу заказов что заказ отправлен в работу Возможно в доступе добавить Работа с поставщиками
    }
}
