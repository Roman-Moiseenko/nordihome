<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Cabinet\Application\DTOs\Pages\CabinetViewData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Storefront\Application\DTOs\ClientContext;

readonly class PageCabinetQuery
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    )
    {

    }
    public function execute(ClientContext $context): CabinetViewData
    {
        $client = $this->clientRepository->findById($context->id);
        $meta = new SeoData('Мой кабинет | ' . $client->fullName->getValue(), '');

        return new CabinetViewData(
            meta: $meta
        );
    }
}
