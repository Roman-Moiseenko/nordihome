<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Cabinet\Application\DTOs\Pages\WishViewData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Storefront\Application\DTOs\ClientContext;

readonly class PageWishQuery
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    )
    {

    }
    public function execute(ClientContext $context): WishViewData
    {
        $client = $this->clientRepository->findById($context->id);
        $meta = new SeoData('Избранное | ' . $client->fullName->getValue(), '');

        return new WishViewData(
            meta: $meta
        );
    }
}
