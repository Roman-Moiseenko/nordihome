<?php

namespace App\Modules\Order\Application\Actions;

use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderAddition;
use App\Modules\Order\Infrastructure\Models\OrderItem;
use App\Modules\Shared\Domain\Entities\UserPermission;

class ViewOrderUseCase
{

    public function __construct()
    {

    }

    public function execute(int $id, UserPermission $permission)
    {
        /** @var Order $order */
        $order = Order::find($id);
        return array_merge($this->OrderToArray($order), [
            'in_stock' => $order->items()->where('preorder', false)->get()->map(fn(OrderItem $item) => $this->OrderItemToArray($item)),
            'pre_order' => $order->items()->where('preorder', true)->get()->map(fn(OrderItem $item) => $this->OrderItemToArray($item)),
            'items' => $order->items()->get()->map(fn(OrderItem $item) => $this->OrderItemToArray($item)),
            'additions' => $order->additions()->get()->map(fn(OrderAddition $orderAddition) => $this->OrderAdditionToArray($orderAddition)),
            'client_id' => $order->client_id,
            'amount' => [
                'base' => $order->getBaseAmount(),
                'manual' => (int)$order->manual,
                'discount' => $order->discount_amount,
                'total' => $order->getTotalAmount(),
                'addition' => $order->getAdditionsAmount(),
                'promotions' => $order->getDiscountPromotions(),
                'coupon' => $order->getCoupon(),
                'percent' => ($order->getBaseAmountNotDiscount() == 0) ? 0 : ceil($order->manual / $order->getBaseAmountNotDiscount() * 100 * 10) / 10,
                'payment' => $order->getPaymentAmount(),
                'refund' => $order->getRefundAmount(),
            ],
            'emails' => is_null($order->shopper_id) ? [] : array_select($order->shopper->getEmails()),
            'shoppers' => [], //is_null($order->client) ? [] : $order->client->organizations,
            'reserve' => $order->getReserveTo(),
            'payments' => $order->payments()->get()->map(fn(OrderPayment $payment) => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'method_text' => $payment->methodText(),
            ]),
            'movements' => $order->movements()->get()->map(fn(MovementDocument $movement) => [
                'id' => $movement->id,
                'number' => $movement->number,
                'status_text' => $movement->statusHTML(),
            ]),
            'expenses' => $order->expenses()->get()->map(fn(OrderExpense $expense) => [
                'id' => $expense->id,
                'number' => $expense->number,
                'created_at' => $expense->created_at,
                'status_text' => $expense->statusHTML(),
                'is_canceled' => $expense->isCanceled(),
                'is_completed' => $expense->isCompleted(),
            ]),
            'weight' => $order->getWeight(),
            'volume' => $order->getVolume(),
        ]);
    }


    private function OrderToArray(Order $order): array
    {
        $status_out = -1;
        $status_pay = -1;

        if ($order->isPrepaid()) $status_pay = 0.5;
        if ($order->isPaid()) $status_pay = 1;
        if ($order->isAwaiting() || $order->isPrepaid()) {
            if (!is_null($order->invoice) && $order->invoice->created_at->lte(now()->subDays(3))) {
                $status_pay = 0;
            }
        }
        if ($order->isPaid() && $order->getPaymentAmount() > $order->getTotalAmount()) $status_pay = 2;
        if ($status_pay > -1) $status_out = $order->getPercentIssued();

        if ($order->isCompleted()) {
            $status_out = 1;
            if ($order->getPaymentAmount() > $order->getTotalAmount()) {
                $status_pay = 2;
            } else {
                $status_pay = 1;
            }
        }
        return array_merge($order->toArray(), [
            'staff' => is_null($order->staff_id) ? 'Не назначен' : $order->staff->fullName,
            'user' => [
                'name' => $order->client->fullName,
                'phone' => $order->client->phone,
            ],
            'amount' => $order->getTotalAmount(),
            'refund' => $order->getRefundAmount(),
            'status' => [
                'is_new' => $order->isNew(),
                'is_manager' => $order->isManager(),
                'is_awaiting' => $order->isAwaiting(),
                'is_prepaid' => $order->isPrepaid(),
                'is_paid' => $order->isPaid(),
                'is_completed' => $order->isCompleted(),
                'is_canceled' => $order->isCanceled(),
                'in_work' => $order->inWork(),
            ],

            'status_text' => $order->statusHtml(),
            'has_cancel' => !($order->inWork() || $order->isCanceled() || $order->isCompleted()),
            'status_pay' => $status_pay,
            'status_out' => $status_out,
        ]);
    }


    private function OrderItemToArray(OrderItem $item): array
    {
        $quantity_sell = $item->product->getQuantitySell();
        $quantity_order = $item->order->getQuantityProduct($item->product_id, false);
        $refund = $item->getRefund();

        return array_merge($item->toArray(), [
            'percent' => $item->getPercent(),
            'product' => array_merge($item->product->toArray(), [
                'weight' => $item->product->weight(),
                'volume' => ceil($item->product->volume() * 1000) / 1000,
                'measuring' => $item->product->measuring->name,
                'has_promotion' => $item->product->hasPromotion(),
                //'quantity_sell' => $item->product->getQuantitySell(),
            ]),
            'supply_stack' => is_null($item->supply_stack_id) ? null : [
                'id' => $item->supplyStack->id,
                'status_text' => $item->supplyStack->status(),
                'supply_id' => $item->supplyStack->supply_id,
            ],
            'remains' => $item->getRemains(),
            'reserves' => ($item->reserves()->count() == 0) ? null : $item->reserves,
            'storages' => Storage::orderBy('name')->get()->map(function (Storage $storage) use ($item) {

                $storageItem = $storage->getItem($item->product_id);
                $orderReserve = is_null($storageItem) ? null : $item->getReserveByStorageItem($storageItem->id);
                return [
                    'id' => $storage->id,
                    'name' => $storage->name,
                    'reserve' => is_null($orderReserve) ? 0 : (float)$orderReserve->quantity,
                    'quantity' => is_null($storageItem) ? 0 : (float)$storageItem->quantity,
                    'reserve_other' => is_null($storageItem) ? 0 : $storageItem->getQuantityReserve($item->order_id),
                ];
            }),
            'quantity_sell' => $quantity_order + $quantity_sell,
            'refund' => $refund == 0 ? null : $refund,
        ]);
    }

    private function OrderAdditionToArray(OrderAddition $orderAddition): array
    {
        $refund = $orderAddition->getRefund();
        return array_merge($orderAddition->toArray(), [
            'calculate' => $orderAddition->getAmount(),
            'name' => $orderAddition->addition->name,
            'manual' => $orderAddition->addition->manual,
            'base' => $orderAddition->addition->base,
            'is_quantity' => $orderAddition->addition->is_quantity,
            'remains' => $orderAddition->getRemains(),
            'refund' => $refund == 0 ? null : $refund,
        ]);
    }
}
