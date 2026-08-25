<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;

readonly class SetStatusOrderUseCase
{
    public function __construct(
        private OrderLoggerServiceInterface   $logger,
        private OrderRepositoryInterface      $repository,
        private SetStatusLeadFromOrderUseCase $leadFromOrderUseCase,

    )
    {
    }

    //TODO Возможно через DTO
    public function execute(
        int         $orderId,
        OrderStatus $status,
        ?string     $comment = null,
        ?string     $numberDocument = null,
        ?string     $dateDocument = null,
    ): void
    {
        $orderEntity = $this->repository->getById($orderId);
        $orderEntity->addStatus($status, $comment, $numberDocument, $dateDocument);
        $this->repository->save($orderEntity);

        $comment = !is_null($comment) ? " ($comment)" : '';
        $this->logger->log(
            orderId: $orderEntity->id,
            action: 'Смена статуса',
            value: OrderStatus::STATUSES[$status->getValue()] . $comment
        );

        $this->leadFromOrderUseCase->execute($orderEntity->id, $status->getValue());

        //   $this->service->canceled($event->order->lead, Lead::CANCELED_ORDER_MANAGER);
    }
}
