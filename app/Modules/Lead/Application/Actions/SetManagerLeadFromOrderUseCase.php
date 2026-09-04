<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;

readonly class SetManagerLeadFromOrderUseCase
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
    ){}

    public function execute(int $orderId, ?int $staffId): void
    {
        $lead = $this->leadRepository->findByOrderId($orderId);
        $lead->staffId = $staffId;
        $this->leadRepository->save($lead);
    }


}
