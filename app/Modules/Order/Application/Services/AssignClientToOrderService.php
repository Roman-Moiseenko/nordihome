<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Lead\Application\Actions\SetClientLeadByOrderIdUseCase;
use App\Modules\Order\Application\Actions\Order\SetClientOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\AssignClientToOrderData;

use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

/**
 * Назначение клиента заказу и лиду
 */
readonly class AssignClientToOrderService
{

    public function __construct(
        private SetClientOrderUseCase $setClientOrderUseCase,
        private SetClientLeadByOrderIdUseCase $setClientLeadByOrderIdUseCase,
        private TransactionManagerInterface $transactionManager
    )
    {
    }
    public function execute(AssignClientToOrderData $dto, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $this->transactionManager->execute(function () use ($dto) {
            //TODO Возможно проверка на наличие клиента
            $this->setClientOrderUseCase->execute($dto);
            $this->setClientLeadByOrderIdUseCase->execute($dto);
        });

    }
}
