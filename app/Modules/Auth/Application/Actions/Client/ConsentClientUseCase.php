<?php

namespace App\Modules\Auth\Application\Actions\Client;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\PersonalDataConsent;
use InvalidArgumentException;

readonly class ConsentClientUseCase
{

    public function __construct(private ClientRepositoryInterface $clientRepository)
    {
    }

    public function execute(?int $clientId): void
    {
        if (is_null($clientId)) throw new InvalidArgumentException('Нет id Client');
        $client = $this->clientRepository->findById($clientId);

        $consent = new PersonalDataConsent(
            policyVersion: 'v1 от 01.01.2026',
        );
        $client->dataConsent = $consent;
        $this->clientRepository->save($client);
    }
}
