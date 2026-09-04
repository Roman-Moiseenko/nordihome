<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Auth\Application\DTOs\Client\ClientIndexData;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Lead\Application\DTOs\Lead\LeadViewData;
use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\ViewOrderUseCase;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;

readonly class IndexByStatusLeadUseCase
{

    public function __construct(
        private LeadRepositoryInterface $repository,
        private StaffRepositoryInterface $staffRepository,
        private ClientRepositoryInterface $clientRepository,
        private OrderRepositoryInterface $orderRepository,
        private ViewOrderUseCase $viewOrderUseCase,
    )
    {
    }

    /**
     * @param int $staffId
     * @param string $status
     * @return LeadViewData[]
     */
    public function execute(int $staffId, string $status): array
    {
        $staff = $this->staffRepository->findById($staffId);
        \Log::info(json_encode($staff->positions->toArrayOfStrings()));

        /** @var StaffPosition $position */
        foreach ($staff->positions->getPositions() as $position) {

            if ($position->isAdmin() || $position->isSupervisor() || $status == LeadStatusValue::NEW_LEAD) {

                $leads = $this->repository->findByStatus($status);
                return $this->hydrate($leads);
            }
        }

        $leads = $this->repository->findByStatus($status, $staffId);

        return $this->hydrate($leads);

    }

    private function hydrate(array $leads): array
    {
        return array_map(function (LeadEntity $lead) {
            if (!is_null($lead->clientId)) {
                $clientEntity = $this->clientRepository->findById($lead->clientId);
                $client = ClientIndexData::fromEntity($clientEntity);
            } else {
                $client = null;
            }

            if (!is_null($lead->orderId)) {

                $order = $this->viewOrderUseCase->execute($lead->orderId);
            } else {
                $order = null;
            }

            //MAINDO получить все leads

            return new LeadViewData(
                id: $lead->id,
                finishedAt: $lead->finishedAt?->format('Y-m-d'),
                createdAt: $lead->createdAt?->format('Y-m-d'),
                type: $lead->leadableType,
                status: $lead->status->value->getValue(),
                data: $lead->data,
                client: $client,
                order: $order,
                leads: [],
            );


        }, $leads);
    }
}
