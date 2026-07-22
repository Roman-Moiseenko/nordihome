<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Actions\Client;

use App\Modules\Auth\Application\DTOs\Client\FindClientByContactData;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;

readonly class FindClientByContactUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    ) {}

    public function execute(FindClientByContactData $dto): ?ClientEntity
    {
        if ($dto->phone) {
            $client = $this->clientRepository->findByPhone(new PhoneNumber($dto->phone));
            if ($client) {
                return $client;
            }
        }

        if ($dto->email) {
            $client = $this->clientRepository->findByEmail(new Email($dto->email));
            if ($client) {
                return $client;
            }
        }

        return null;
    }
}
