<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;

/**
 * Используется только в сервисах по установке статусов или в сервисах создания заказа (new())
 */
readonly class SetStatusOrderUseCase
{
    public function __construct(
        private OrderLoggerServiceInterface   $logger,
        private OrderRepositoryInterface      $repository,
        private TransactionManagerInterface $transactionManager,
    )
    {
    }

    public function execute(StatusOrderAssignData $dto): void
    {
        $this->transactionManager->execute(function () use ($dto) {

            $orderEntity = $this->repository->getById($dto->orderId);
            $orderEntity->addStatus($dto->status, $dto->comment, $dto->numberDocument, $dto->dateDocument);
            $this->repository->save($orderEntity);

            $comment = !is_null($dto->comment) ? " ($dto->comment)" : '';
            $this->logger->log(
                orderId: $orderEntity->id,
                action: 'Смена статуса',
                value: OrderStatus::STATUSES[$dto->status->getValue()] . $comment
            );
        });

    }
}
