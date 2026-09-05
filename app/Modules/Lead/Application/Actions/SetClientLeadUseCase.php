<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\DTOs\Order\AssignClientToOrderData;

readonly class SetClientLeadUseCase
{
    public function __construct(
        private LeadRepositoryInterface $repository
    ) {}

    public function execute(int $leadId, int $clientId): void
    {
        $lead = $this->repository->findById($leadId);

        $lead->clientId = $clientId;

        $this->repository->save($lead);
    }
}
