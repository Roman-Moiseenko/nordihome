<?php

namespace App\Modules\Auth\Application\Actions\Client;

use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;

readonly class FindClientsByParamsUseCase
{
    public function __construct(private ClientRepositoryInterface $clientRepository)
    {
    }
    public function execute(string $search): array
    {
        return $this->clientRepository->findByParams($search);
    }
}
