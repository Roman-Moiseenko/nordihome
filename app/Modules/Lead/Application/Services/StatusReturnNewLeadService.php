<?php

namespace App\Modules\Lead\Application\Services;

use App\Modules\Lead\Application\Actions\SetManagerLeadFromOrderUseCase;
use App\Modules\Lead\Application\Actions\SetManagerLeadUseCase;
use App\Modules\Lead\Application\Actions\SetStatusLeadUseCase;
use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\Order\SetManagerOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusReturnNewLeadService
{
    public function __construct(
        private SetStatusLeadUseCase           $statusLeadUseCase,
        private SetManagerLeadUseCase $setManagerLeadUseCase,

        private SetStatusOrderUseCase          $setStatusOrderUseCase,
        private SetManagerOrderUseCase         $setManagerOrderUseCase,

        private LeadRepositoryInterface        $leadRepository,

        private TransactionManagerInterface    $transactionManager,
    )
    {
    }

    public function execute(int $leadId, int $staffId, UserPermission $permission): void
    {
        if (!$permission->can('lead.lead.edit')) throw new AccessDeniedException();

        $this->transactionManager->execute(function () use ($leadId, $staffId) {
            //1. Меняем статус
            $this->statusLeadUseCase->execute($leadId, LeadStatusValue::NEW_LEAD);

            $leadEntity = $this->leadRepository->findById($leadId);

            //2. Сбрасываем менеджера
             $this->setManagerLeadUseCase->execute($leadId, null);

            //3. Если есть заказ, то в нем меняем статус и сбрасываем менеджера
            if (!is_null($leadEntity->orderId)) {
                $dto = new StatusOrderAssignData($leadEntity->orderId, OrderStatus::new(), 'Возврат в пул лидов');
                $this->setStatusOrderUseCase->execute($dto);
                $this->setManagerOrderUseCase->execute($leadEntity->orderId, null);
            }
        });


    }
}
