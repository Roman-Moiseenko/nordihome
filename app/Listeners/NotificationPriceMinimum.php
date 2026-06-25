<?php

namespace App\Listeners;

use App\Events\PriceHasMinimum;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;


class NotificationPriceMinimum
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(PriceHasMinimum $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::supervisor());
                //FIXME Модуль Notification - через RecipientResolverInterface
      /*

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Установлена цена ниже минимальной',
                $event->item->product->name . ' - ' . price($event->item->sell_cost),
                route('admin.order.show', $event->item->order),
                'badge-russian-ruble'
            ));
        }
*/
        //throw new \DomainException('Цена продажи меньше установленной минимальной для товара ' . $event->item->product->name);
    }
}
