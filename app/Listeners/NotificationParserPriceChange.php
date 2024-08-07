<?php

namespace App\Listeners;

use App\Events\ParserPriceHasChange;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationParserPriceChange
{
    private StaffRepository $repository;

    public function __construct(StaffRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     */
    public function handle(ParserPriceHasChange $event): void
    {
        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_PRODUCT);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Изменилась цена в Икеа',
                "Артикул товара " . $event->productParser->product->code,
                route('admin.product.edit', $event->productParser->product),
                'package-open'
            ));
        }
    }
}
