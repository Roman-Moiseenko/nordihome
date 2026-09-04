<?php

namespace App\Modules\Order\Application\Services\StatusServices;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SendMailCancelOrderClientUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusCancelOrderService
{
    public function __construct(
        private SetStatusOrderUseCase            $statusOrderUseCase,
        private SendMailCancelOrderClientUseCase $mailCancelOrderClientUseCase,
        private SetStatusLeadFromOrderUseCase $leadFromOrderUseCase,
    )
    {
    }

    public function execute(int $orderId, string $comment, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $dto = new StatusOrderAssignData(
            orderId: $orderId,
            status: OrderStatus::cancelled(),
            comment: $comment,
        );

        $this->statusOrderUseCase->execute($dto);

        $this->leadFromOrderUseCase->execute($dto->orderId, LeadStatusValue::CANCELLED);

        $this->mailCancelOrderClientUseCase->execute($orderId);
    }
}
