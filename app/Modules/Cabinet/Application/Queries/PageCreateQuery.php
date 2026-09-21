<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Cabinet\Application\DTOs\Pages\CreateOrderData;
use App\Modules\Cart\Application\Actions\GetCartQuery;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

readonly class PageCreateQuery
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private GetCartQuery              $getCartUseCase,
    )
    {

    }

    public function execute(ClientContext $context): CreateOrderData
    {
        if (is_null($context->id)) {
            $title = 'Гость';
        } else {
            $client = $this->clientRepository->findById($context->id);
            $title = $client->fullName->getValue();
        }

        $meta = new SeoData('Оформление заказа | ' . $title, '');
        $cartInfo = $this->getCartUseCase->execute($context);
        return new CreateOrderData(
            meta: $meta,
            cartInfo: $cartInfo,
        );
    }
}
