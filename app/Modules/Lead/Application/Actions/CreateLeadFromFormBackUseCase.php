<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Auth\Application\DTOs\Client\FindClientByContactData;
use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\ValueObjects\LeadDataField;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;

readonly class CreateLeadFromFormBackUseCase
{
    public function __construct(
        private LeadRepositoryInterface $leadRepository,
        private FindClientByContactUseCase $findClientByContactUseCase,
    ) {}

    public function execute(LeadSourceData $dto): LeadEntity
    {
        $lead = new LeadEntity(
            leadableId: $dto->id,
            leadableType: $dto->able,
            data: [],
        );

        $phone = null;
        $email = null;

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

        // Ищем клиента по phone или email через Auth-модуль
        $client = $this->findClientByContactUseCase->execute(
            new FindClientByContactData(phone: $phone, email: $email)
        );
        if ($client) $lead->clientId = $client->id;

        $lead->addStatus(new LeadStatusValue(LeadStatusValue::NEW_LEAD));

        return $this->leadRepository->save($lead);
    }
}
