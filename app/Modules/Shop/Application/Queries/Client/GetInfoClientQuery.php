<?php

namespace App\Modules\Shop\Application\Queries\Client;

use App\Modules\Shop\Application\DTOs\Client\ClientInfoData;
use App\Modules\Shop\Infrastructure\Persistence\Query\ClientQueryRepository;

class GetInfoClientQuery
{

    public function __construct(private ClientQueryRepository $repository)
    {

    }
    public function execute(int $clientId): ClientInfoData
    {
        $data = $this->repository->getInfoClient($clientId);

        return new ClientInfoData(
            id: $clientId,
        );
    }
}
