<?php

namespace App\Listeners;

use App\Events\ProductHasFastCreate;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationProductFastCreat
{
    private StaffRepository $repository;

    /**
     * Create the event listener.
     */
    public function __construct(StaffRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     */
    public function handle(ProductHasFastCreate $event): void
    {
        $staffs = $this->repository->getStaffsByCode(Responsibility::MANAGER_PRODUCT);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Создан новый товар в продажах ',
                "Артикул товара " . $event->product->code,
                route('admin.product.edit', $event->product),
                'package-open'
            ));
        }
    }
}
