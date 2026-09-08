<?php

namespace App\Modules\Order\Application\Actions\OrderLogger;

use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Domain\Entities\OrderLoggerEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;

readonly class CreateOrderLoggerUseCase
{
    public function __construct(
        private OrderLoggerRepositoryInterface $loggerRepository,
        private OrderRepositoryInterface $orderRepository
    )
    {

    }


    public function execute(int $orderId, OrderLoggerCreateData $dto): OrderLoggerEntity
    {
        $orderEntity = $this->orderRepository->getById($orderId);
        //if (is_null($orderEntity)) throw new \DomainException("Заказ не существует");
        if ($orderEntity->status->value->isFinished()) throw new \DomainException("Заказ завершен. Доступ закрыт");

        $user = auth()->user();
        $staffId = $user && $user->isStaff() ? $user->profileable_id : null;
        $logEntity = new OrderLoggerEntity(
            orderId: $orderId,
            staffId: $staffId,
            action: $dto->action,
        );
        $logEntity->createdAt = new \DateTimeImmutable();
        $logEntity->object = $dto->object;
        $logEntity->value = $dto->value;
        $logEntity->link = $dto->link;
        $logEntity->old = $dto->old;


        return $this->loggerRepository->save($logEntity);
    }
}
