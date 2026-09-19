<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Cabinet\Application\DTOs\Pages\OptionsViewData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Storefront\Application\DTOs\ClientContext;

readonly class PageOptionsQuery
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
    )
    {

    }
    public function execute(ClientContext $context): OptionsViewData
    {
        $client = $this->clientRepository->findById($context->id);

        //TODO подписки и др
        //$subscriptions = Subscription::orderBy('name')->active()->get();

        $meta = new SeoData('Рассылки & Уведомления | ' . $client->fullName->getValue(), '');

        return new OptionsViewData(
            meta: $meta
        );
    }
}
