<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;

readonly class SetStatusLeadUseCase
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
    ) {}

    public function execute(int $leadId, string $status): void
    {
        $lead = $this->leadRepository->findById($leadId);

        $statusLead = new LeadStatusValue($status);

        $lead->addStatus($statusLead);
        $this->leadRepository->save($lead);
    }
}
