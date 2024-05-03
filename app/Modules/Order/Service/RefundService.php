<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasRefund;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderRefund;
use Illuminate\Support\Facades\Auth;

class RefundService
{
    public function create(Order $order, string $comment)
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $refund = OrderRefund::register($order->id, $staff->id, $comment);

        foreach ($order->items as $item) {
            if ($remains = $item->getRemains()) {
                if ($remains != 0) {
                    $refund->items()->create([
                        'order_item_id' => $item->id,
                        'quantity' => $remains,
                    ]);
                }
            }
        }

        foreach ($order->additions as $addition) {
            if ($remains = $addition->getRemains()) {
                if ($remains != 0) {
                    $refund->additions()->create([
                        'order_addition_id' => $addition->id,
                        'amount' => $remains,
                    ]);
                }
            }
        }
        $refund->refresh();
        event(new OrderHasRefund($order));
    }
}
