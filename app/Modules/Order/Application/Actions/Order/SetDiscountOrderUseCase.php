<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Application\DTOs\Order\DiscountOrderData;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetDiscountOrderUseCase
{

    public function __construct(
        private OrderRepositoryInterface    $repository,
        private OrderLoggerServiceInterface $logger,
    )
    {
    }

    public function execute(int $orderId, DiscountOrderData $dto, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $orderEntity = $this->repository->getById($orderId);

        $old_manual = $orderEntity->manual; //Для Логирования

        $baseAmount = 0; //База для скидки
        foreach ($orderEntity->items as $item) {
            if (is_null($item->discountId))
                $baseAmount += $item->baseCost * $item->quantity;
        }
        if ($baseAmount == 0) throw new \DomainException('В заказе нет товаров для установки ручной скидки');

        if ($dto->isPercent()) {
            $percentItem = $dto->percent / 100;

        } else {
            $percentItem = $dto->manual / $baseAmount;
        }
        foreach ($orderEntity->items as $item) {
            if (is_null($item->discountId)) {
                $sellCost = ($item->baseCost * (1 - $percentItem));
                $item->update(sellCost: $sellCost);
            }
        }
        $orderEntity->manual = $dto->isPercent()
            ? ($dto->percent * $baseAmount / 100)
            : $dto->manual;

        $orderEntity->recalculateTotals();

        $this->repository->save($orderEntity);
        $value = $dto->isPercent() ? "$dto->percent %" : price($dto->manual);

        $this->logger->log(orderId: $orderEntity->id, action: 'Установлена общая скидка',
            value: $value, old: price($old_manual));
    }
}
