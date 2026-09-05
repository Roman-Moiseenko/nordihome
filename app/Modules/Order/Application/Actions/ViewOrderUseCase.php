<?php
declare(strict_types=1);

namespace App\Modules\Order\Application\Actions;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Order\Application\DTOs\AmountOrderData;
use App\Modules\Order\Application\DTOs\ClientOrderData;
use App\Modules\Order\Application\DTOs\OrderAdditionViewData;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemViewData;
use App\Modules\Order\Application\DTOs\OrderStatusViewData;
use App\Modules\Order\Application\DTOs\OrderViewData;
use App\Modules\Order\Domain\Entities\OrderHistoryStatusEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderAddition;
use App\Modules\Order\Infrastructure\Models\OrderItem;

readonly class ViewOrderUseCase
{

    public function __construct(
        private OrderRepositoryInterface  $repository,
        private ClientRepositoryInterface $clientRepository,
        private GetProductItemDataUseCase $getProductItemData,
        private GetAdditionDataUseCase    $getAdditionDataUseCase,
        private StaffRepositoryInterface $staffRepository,
    )
    {
    }

    public function execute(int $id): OrderViewData
    {
        //MAINDO Убрать расчеты, данные должны пересчитываться при изменении Item и Addition

        // if ($permission->can('order.order.edit')) throw new \DomainException('Нет доступа к редактированию заказов');

        $orderEntity = $this->repository->getById($id);

        // --- client ---
        if (!is_null($orderEntity->clientId)) {
            $clientEntity = $this->clientRepository->findById($orderEntity->clientId);
            $clientDto = new ClientOrderData(
                id: $clientEntity->id,
                fullName: $clientEntity->fullName?->getValue() ?? '',
                email: $clientEntity->email?->value ?? '',
                phone: $clientEntity->phone?->getValue() ?? '',
                priceType: $clientEntity->priceType->value,
            );
        } else {
            $clientDto = null;
        }

        // --- items + расчёт сумм ---
        $items = [];
        $inStock = [];
        $preOrder = [];

        $baseAmount = 0.0;
        $promotionsAmount = 0.0;
        $totalWeight = 0.0;
        $totalVolume = 0.0;

        foreach ($orderEntity->items as $item) {
            $productItemData = $this->getProductItemData->execute($item->productId);

            // Признак скидки и процент
            $isDiscount = $item->discountId !== null;
            $percentDiscount = 0.0;
            if ($item->baseCost > 0) {
                $percentDiscount = ceil(($item->baseCost - $item->sellCost) / $item->baseCost * 100 * 10) / 10;
            }

            $itemDto = new OrderItemViewData(
                id: $item->id,
                product: $productItemData,
                baseCost: $item->baseCost,
                sellCost: $item->sellCost,
                quantity: $item->quantity,
                preorder: $item->preorder,
                isDiscount: $isDiscount,
                percentDiscount: $percentDiscount,
                comment: $item->comment,
                assemblage: $item->assemblage,
                packing: $item->packing,
            );

            if ($item->preorder) {
                $preOrder[] = $itemDto;
            } else {
                $inStock[] = $itemDto;
            }
            $items[] = $itemDto;

            // Суммы
            $baseAmount += $item->baseCost * $item->quantity;

            // Промо-скидки
            if (!is_null($item->discountId)) {
                $promotionsAmount += ($item->baseCost - $item->sellCost) * $item->quantity;
            }
            $totalWeight += (float)$productItemData->weight * $item->quantity;
            $totalVolume += (float)$productItemData->volume * $item->quantity;
        }

        // --- additions ---
        $additions = [];
        $additionsAmount = 0.0;
        foreach ($orderEntity->additions as $orderAddition) {

            $addition = $this->getAdditionDataUseCase->execute($orderAddition->additionId);

            $additionDto = new OrderAdditionViewData(
                id: $orderAddition->id,
                amount: $orderAddition->amount,
                comment: $orderAddition->comment,
                quantity: $orderAddition->quantity,
                addition: $addition,
            );

            $additions[] = $additionDto;
            $additionsAmount += $orderAddition->amount;
        }

        // --- statuses ---
        $statuses = array_map(function (OrderHistoryStatusEntity $entity) {
            return OrderStatusViewData::fromEntity($entity);
        }, $orderEntity->statuses);

        // Последний статус — текущий
        $currentStatus = OrderStatusViewData::fromEntity($orderEntity->status);

        // --- amount ---
        $discountAmount = $orderEntity->discountAmount ?? 0.0;
        $couponAmount = $orderEntity->couponAmount ?? 0.0;
        $manualAmount = $orderEntity->manual;

        // total = base - promotions + additions + discount - coupon (как в getTotalAmount, но без refund)
        $totalAmount = ceil(
            ($baseAmount - $promotionsAmount)
            + $additionsAmount
            + $discountAmount
            - $couponAmount
        );

        $amountDto = new AmountOrderData(
            base: $baseAmount,
            addition: $additionsAmount,
            manual: $manualAmount,
            percent: $baseAmount == 0 ? 0 : $manualAmount / $baseAmount * 100,
            promotions: $promotionsAmount,
            coupon: $couponAmount,
            discount: $discountAmount,
            total: $totalAmount,
            weight: ceil($totalWeight * 1000) / 1000,
            volume: ceil($totalVolume * 10000) / 10000,
        );


        return new OrderViewData(
            id: $orderEntity->id,
            number: $orderEntity->number,
            staffId: $orderEntity->staffId,
            staffName: is_null($orderEntity->staffId) ? null : $this->staffRepository->findById($orderEntity->staffId)->fullName->getValue(),
            traderId: $orderEntity->traderId,
            priceType: $orderEntity->priceType->value,
            discountAmount: $orderEntity->discountAmount,
            couponAmount: $orderEntity->couponAmount,
            manual: $orderEntity->manual,
            amount: $amountDto,
            isPickup: $orderEntity->isPickup,
            address: $orderEntity->address?->getFullAddress() ?? null,
            comment: $orderEntity->comment,
            commentClient: $orderEntity->commentClient,

            client: $clientDto,
            items: $items,
            preOrder: $preOrder,
            inStock: $inStock,
            additions: $additions,
            statuses: $statuses,
            status: $currentStatus->value,
        );


        //MAINDO Удалить после полного тестирования !!
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
              //  'percent' => ($order->getBaseAmountNotDiscount() == 0) ? 0 : ceil($order->manual / $order->getBaseAmountNotDiscount() * 100 * 10) / 10,
                'payment' => $order->getPaymentAmount(),
                'refund' => $order->getRefundAmount(),
            ],
            'emails' => is_null($order->shopper_id) ? [] : array_select($order->shopper->getEmails()),
            'shoppers' => [], //is_null($order->client) ? [] : $order->client->organizations,
            //    'reserve' => $order->getReserveTo(),
            /*
            'payments' => $order->payments()->get()->map(fn(OrderPayment $payment) => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'method_text' => $payment->methodText(),
            ]),
            */
            /*
        'movements' => $order->movements()->get()->map(fn(MovementDocument $movement) => [
            'id' => $movement->id,
            'number' => $movement->number,
            'status_text' => $movement->statusHTML(),
        ]),
        */
            /*
            'expenses' => $order->expenses()->get()->map(fn(OrderExpense $expense) => [
                'id' => $expense->id,
                'number' => $expense->number,
                'created_at' => $expense->created_at,
                'status_text' => $expense->statusHTML(),
                'is_canceled' => $expense->isCanceled(),
                'is_completed' => $expense->isCompleted(),
            ]),
            */
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
        ///$refund = $orderAddition->getRefund();
        return array_merge($orderAddition->toArray(), [
            'calculate' => $orderAddition->getAmount(),
            'name' => $orderAddition->addition->name,
            'manual' => $orderAddition->addition->manual,
            'base' => $orderAddition->addition->base,
            'is_quantity' => $orderAddition->addition->is_quantity,
            // 'remains' => $orderAddition->getRemains(),
            // 'refund' => $refund == 0 ? null : $refund,
        ]);
    }
}
