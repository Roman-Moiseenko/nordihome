<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Entity\Admin;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Order\Entity\Order\OrderItem;
use Illuminate\Support\Facades\Auth;

class SupplyService
{

    public function add_stack(OrderItem $item, int $storage_id): SupplyStack
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $stack = SupplyStack::register($item->product_id, $item->quantity, $staff->id, $storage_id, 'Заказ # ' . $item->order->htmlNum());

        $item->supply_stack_id = $stack->id;
        $item->save();

        return $stack;
    }
}
