<?php

namespace App\Listeners;

use App\Events\ProductHasBlocked;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationProductBlocked
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(ProductHasBlocked $event): void
    {
        if ($event->product->getQuantity() == 0) return;
        //Уведомляем, если кол-во товара на остатках > 0
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Товар снят с продажи ',
                "Артикул товара " . $event->product->code . ' Общее кол-во = ' . $event->product->getQuantity(),
                route('admin.product.product.edit', $event->product),
                'package-open'
            ));
        }*/
    }
}
