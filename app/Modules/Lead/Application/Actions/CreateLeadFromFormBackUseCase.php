<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Auth\Application\DTOs\Client\FindClientByContactData;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\ValueObjects\LeadDataField;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;

readonly class CreateLeadFromFormBackUseCase
{
    public function __construct(
        private LeadRepositoryInterface    $leadRepository,
        private FindClientByContactUseCase $findClientByContactUseCase,
        private OrderRepositoryInterface   $orderRepository,
    ) {}

    public function execute(LeadSourceData $dto): LeadEntity
    {
        $lead = new LeadEntity(
            leadableId: $dto->id,
            leadableType: $dto->able,
            data: [],
        );
        $lead->orderId = $dto->orderId;
        $phone = null;
        $email = null;
        if (!is_null($dto->orderId)) {
            $lead->orderId = $dto->orderId;
            //Проверяем, еть ли у заказа Менеджер, тогда отдаем ему лид
            $order = $this->orderRepository->getById($dto->orderId);
            $lead->staffId = $order->staffId;
        }


        // Преобразуем data в VO и ищем телефон/email
        foreach ($dto->data as $key => $value) {
            $lead->addDataField(new LeadDataField(
                name: $key,
                value: $value,
            ));

            if ($key === 'name' && !empty($value)) {
                $lead->name = $value;
            }

            if ($key === 'phone' && !empty($value)) {
                $phone = $value;
            }

            if ($key === 'email' && !empty($value)) {
                $email = $value;
            }
        }
        if (is_null($lead->name)) $lead->name = 'Заказ с сайта';

        // Ищем клиента по phone или email через Auth-модуль
        $client = $this->findClientByContactUseCase->execute(
            new FindClientByContactData(phone: $phone, email: $email)
        );
        if ($client) $lead->clientId = $client->id;

        $lead->addStatus(new LeadStatusValue(LeadStatusValue::NEW_LEAD));

        return $this->leadRepository->save($lead);
    }
}
