<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;

readonly class SetNameLeadUseCase
{
    public function __construct(
        private LeadRepositoryInterface $repository
    ) {}

    public function execute(int $leadId, string $name): void
    {
        $lead = $this->repository->findById($leadId);

        $lead->name = $name;
        $this->repository->save($lead);
    }
}
