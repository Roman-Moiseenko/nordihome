<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Application\Actions\AdditionGuide\GetAssemblageAdditionUseCase;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetAssemblagesOrderUseCase
{

    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderCalculateService    $orderCalculateService,
        private GetAssemblageAdditionUseCase $assemblageAdditionUseCase,
    )
    {

    }
    public function execute(int $orderId, bool $assemblage, array $ids, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);

        foreach ($orderEntity->items as $item) {
            if (in_array($item->id, $ids)) {
                $item->update(assemblage: $assemblage);
            }
        }

        if (!is_null($assembleGuide = $this->assemblageAdditionUseCase->execute())) {
            if ($assemblage) {
                $orderEntity->addAddition($assembleGuide->id);
            } else { //Если нигде не назначена, удаляем
                $hasAssemble = false;
                foreach ($orderEntity->items as $item) {
                    if ($item->assemblage) $hasAssemble = true;
                }
                if (!$hasAssemble) $orderEntity->removeAdditionBy($assembleGuide->id);
            }
        }

        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);

    }
}
