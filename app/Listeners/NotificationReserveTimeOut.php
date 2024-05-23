<?php

namespace App\Listeners;

use App\Events\ReserveHasTimeOut;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationReserveTimeOut
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(ReserveHasTimeOut $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_ORDER);
        foreach ($staffs as $staff) {
            if ($staff->id == $event->order->manager_id) {
                $staff->notify(new StaffMessage(
                    'Резерв по товару',
                    ($event->timeOut ? 'Закончилось время резерва' : 'Закачивается время резерва, осталось менее 12 ч.') . ' ' . $event->order->htmlNumDate(),
                    route('admin.sales.order.show', $event->order),
                    'baggage-claim'
                ));
            }
        }

        if ($event->timeOut && $event->order->isAwaiting()) {
            //TODO письмо клиенту что резерв закончился
        }

    }
}
