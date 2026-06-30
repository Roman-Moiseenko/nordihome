<?php

namespace App\Listeners;

use App\Events\ParserPriceHasChange;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationParserPriceChange
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(ParserPriceHasChange $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Изменилась цена в Икеа',
                "Артикул товара " . $event->productParser->product->code,
                route('admin.catalog.product.edit', $event->productParser->product),
                'package-open'
            ));
        }
*/
    }
}
