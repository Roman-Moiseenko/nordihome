<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Cabinet\Application\DTOs\Pages\ReviewViewData;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

readonly class PageReviewQuery
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        public GetReviewsClientQuery $getReviewsClientQuery,
    )
    {

    }
    public function execute(ClientContext $context): ReviewViewData
    {
        $client = $this->clientRepository->findById($context->id);
        $meta = new SeoData('Мои отзывы | ' . $client->fullName->getValue(), '');
        //TODO Переделать на получение отзывов через Query
        $reviews = $this->getReviewsClientQuery->execute($client->id);

        return new ReviewViewData(
            meta: $meta,
            reviews: $reviews,
        );
    }
}
