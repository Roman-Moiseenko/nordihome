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

    }
}
