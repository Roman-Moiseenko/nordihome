<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;

readonly class SetManagerLeadUseCase
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
    ){}

    public function execute(int $leadId, ?int $staffId): void
    {
        $lead = $this->leadRepository->findById($leadId);
        $lead->staffId = $staffId;
        $this->leadRepository->save($lead);
    }


}
