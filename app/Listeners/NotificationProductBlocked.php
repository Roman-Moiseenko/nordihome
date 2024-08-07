<?php

namespace App\Listeners;

use App\Events\ProductHasBlocked;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationProductBlocked
{
    private StaffRepository $repository;

    public function __construct(StaffRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     */
    public function handle(ProductHasBlocked $event): void
    {
        if ($event->product->getQuantity() == 0) return;
        //Уведомляем, если кол-во товара на остатках > 0
        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_PRODUCT);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Товар снят с продажи ',
                "Артикул товара " . $event->product->code . ' Общее кол-во = ' . $event->product->getQuantity(),
                route('admin.product.edit', $event->product),
                'package-open'
            ));
        }
    }
}
