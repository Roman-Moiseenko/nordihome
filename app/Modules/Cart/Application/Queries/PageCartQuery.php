<?php

namespace App\Modules\Cart\Application\Queries;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Cart\Application\DTOs\CartViewData;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

readonly class PageCartQuery
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    )
    {

    }

    public function execute(ClientContext $context):CartViewData
    {
        if (is_null($context->id)) {
            $title = 'Гость';
        } else {
            $client = $this->clientRepository->findById($context->id);
            $title = $client->fullName->getValue();
        }

        $meta = new SeoData('Корзина | ' . $title, '');

        return new CartViewData(
            meta: $meta
        );
    }
}
