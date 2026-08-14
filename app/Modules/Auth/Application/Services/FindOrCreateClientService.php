<?php

namespace App\Modules\Auth\Application\Services;

use App\Modules\Auth\Application\Actions\Client\ConsentClientUseCase;
use App\Modules\Auth\Application\Actions\Client\CreateClientUseCase;
use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Auth\Application\Actions\Client\UpdateClientUseCase;
use App\Modules\Auth\Application\DTOs\Client\ClientCreateData;
use App\Modules\Auth\Application\DTOs\Client\ClientUpdateData;
use App\Modules\Auth\Application\DTOs\Client\FindClientByContactData;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shop\Application\DTOs\Checkout\OneClickOrderData;

readonly class FindOrCreateClientService
{

    public function __construct(
        private FindClientByContactUseCase $findClientByContactUseCase,
        private CreateClientUseCase $createClientUseCase,
        private UpdateClientUseCase $updateClientUseCase,
        private ConsentClientUseCase $consentClientUseCase,
    )
    {

    }

    /**
     * @throws \DateMalformedStringException
     */
    public function execute(OneClickOrderData $dto): ClientEntity
    {

        $userPermission = new UserPermission(null, [] , ['auth.buyer.create', 'auth.buyer.edit']);

        if (!$dto->agreement) throw new \InvalidArgumentException('Нет согласия на ПДн');

        $clientDto = new FindClientByContactData(phone: new PhoneNumber($dto->phone)->getValue(),
            email: $dto->email);
        $client = $this->findClientByContactUseCase->execute($clientDto);
        if ($client == null) {
            $clientDto = new ClientCreateData(
                lastName: 'Розничный',
                firstName: 'Клиент',
                email: $dto->email,
                middleName: null,
                phone: new PhoneNumber($dto->phone)->getValue(),
            );
            $client = $this->createClientUseCase->execute($clientDto, $userPermission);
            $this->consentClientUseCase->execute($client->id); //Согласие
        }

        $updateDta = new ClientUpdateData(
            region: $dto->region,
            regionCode: $dto->regionCode,
            street: $dto->address,
            isPickup: $dto->isPickup,
        );
        return $this->updateClientUseCase->execute($client->id, $updateDta, $userPermission);
    }
}
