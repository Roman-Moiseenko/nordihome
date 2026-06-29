<?php

namespace App\Listeners;

use App\Events\ProductHasParsed;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationProductParserNew
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(ProductHasParsed $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Добавлен новый товар через Парсер',
                "Артикул товара " . $event->product->code,
                route('admin.product.product.edit', $event->product),
                'package-open'
            ));
        }
*/
    }
}
