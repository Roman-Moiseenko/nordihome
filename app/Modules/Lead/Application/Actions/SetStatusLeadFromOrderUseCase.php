<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;

readonly class SetStatusLeadFromOrderUseCase
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
    ) {}

    public function execute(int $orderId, string $status): void
    {
        $lead = $this->leadRepository->findByOrderId($orderId);
        $statusLead = new LeadStatusValue($status);

        $lead->addStatus($statusLead);

    }
}
