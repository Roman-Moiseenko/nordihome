<?php

namespace App\Listeners;

use App\Events\PriceHasMinimum;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationPriceMinimum
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(PriceHasMinimum $event): void
    {
        $staffs = $this->staffs->getChief();

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Установлена цена ниже минимальной',
                $event->item->product->name . ' - ' . price($event->item->sell_cost),
                route('admin.order.show', $event->item->order),
                'badge-russian-ruble'
            ));
        }

        //throw new \DomainException('Цена продажи меньше установленной минимальной для товара ' . $event->item->product->name);
    }
}
