<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Auth\Application\DTOs\Client\ClientIndexData;
use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Lead\Application\DTOs\Lead\LeadClientData;
use App\Modules\Lead\Application\DTOs\Lead\LeadCommentData;
use App\Modules\Lead\Application\DTOs\Lead\LeadOrderData;
use App\Modules\Lead\Application\DTOs\Lead\LeadViewData;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Application\Actions\ViewOrderUseCase;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;

readonly class IndexByStatusLeadUseCase
{

    public function __construct(
        private LeadRepositoryInterface $repository,
        private StaffRepositoryInterface $staffRepository,
        private ClientRepositoryInterface $clientRepository,
        private OrderRepositoryInterface $orderRepository,
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

        //Для администратора и руководителя, а также панели New показываем все лиды
        if ($staff->positions->contains(StaffPosition::administrator())
            || $staff->positions->contains(StaffPosition::supervisor())
            || $status == LeadStatusValue::NEW_LEAD) {

            $leads = $this->repository->findByStatus($status);
            return $this->hydrate($leads);
        }

        //Для остальных, только текущего менеджера
        $leads = $this->repository->findByStatus($status, $staffId);

        return $this->hydrate($leads);

    }

    private function hydrate(array $leads): array
    {
        return array_map(function (LeadEntity $lead) {
            if (!is_null($lead->clientId)) {
                $clientEntity = $this->clientRepository->findById($lead->clientId);
                $client = LeadClientData::fromEntity($clientEntity);
            } else {
                $client = null;
            }

            if (!is_null($lead->orderId)) {
                $orderEntity = $this->orderRepository->getById($lead->orderId);
                $order = LeadOrderData::fromEntity($orderEntity);
            } else {
                $order = null;
            }


            return new LeadViewData(
                id: $lead->id,
                staffId: $lead->staffId,
                finishedAt: $lead->finishedAt?->format('Y-m-d'),
                createdAt: $lead->createdAt?->format('Y-m-d'),
                name: $lead->name,
                type: $lead->leadableType,
                status: $lead->status->value->getValue(),
                data: $lead->data,
                client: $client,
                order: $order,
                leads: [],
                comment: $lead->comment,
                comments: LeadCommentData::collect($lead->items),
            );

        }, $leads);
    }
}
