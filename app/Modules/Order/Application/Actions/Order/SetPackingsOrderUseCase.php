<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Application\Actions\AdditionGuide\GetPackingAdditionUseCase;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetPackingsOrderUseCase
{

    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderCalculateService    $orderCalculateService,
        private GetPackingAdditionUseCase $packingAdditionUseCase,
    )
    {
    }
    public function execute(int $orderId, bool $packing, array $ids, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);

        foreach ($orderEntity->items as $item) {
            if (in_array($item->id, $ids)) {
                $item->update(packing: $packing);
            }
        }

        if (!is_null($packingGuide = $this->packingAdditionUseCase->execute())) {
            if ($packing) {
                $orderEntity->addAddition($packingGuide->id);
            } else { //Если нигде не назначена, удаляем
                $hasPacking = false;
                foreach ($orderEntity->items as $item) {
                    if ($item->packing) $hasPacking = true;
                }
                if (!$hasPacking) $orderEntity->removeAdditionBy($packingGuide->id);
            }
        }

        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);

    }
}
