<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\DTOs\Order\AssignClientToOrderData;

readonly class SetClientLeadByOrderIdUseCase
{
    public function __construct(
        private LeadRepositoryInterface $repository
    ) {}

    public function execute(AssignClientToOrderData $dto): void
    {
        $lead = $this->repository->findByOrderId($dto->orderId);

        if (is_null($lead)) return; //Заказ без Лида

        $lead->clientId = $dto->clientId;
        $this->repository->save($lead);
    }
}
