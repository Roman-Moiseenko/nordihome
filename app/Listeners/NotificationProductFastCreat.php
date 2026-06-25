<?php

namespace App\Listeners;

use App\Events\ProductHasFastCreate;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationProductFastCreat
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(ProductHasFastCreate $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Создан новый товар в продажах ',
                "Артикул товара " . $event->product->code,
                route('admin.product.edit', $event->product),
                'package-open'
            ));
        }
*/
    }
}
