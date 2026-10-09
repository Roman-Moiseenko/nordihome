<?php

namespace App\Modules\Shop\Application\Queries\Search;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Search\FullSearchData;
use App\Modules\Shop\Application\DTOs\Search\ItemSearchData;
use App\Modules\Shop\Infrastructure\Persistence\Query\ParserProductSearchQueryRepository;

class ParserSearchQuery
{
    public function __construct(
        private ParserProductSearchQueryRepository $searchQueryRepository,

    )
    {
    }
    public function execute(string $search, ClientContext $clientContext): FullSearchData
    {
        // Оставляем в поисковой строке только цифры
        $digits = preg_replace('/\D/', '', $search);

        // Если цифр нет или их больше 8 — не ищем
        if ($digits === '' || mb_strlen($digits) > 8) {
            return new FullSearchData(
                search: $search,
                products: [],
                categories: [],
                rooms: [],
                recommends: []
            );
        }

        $productItemsRaw = $this->searchQueryRepository->getProductBySearch($digits, FullSearchData::LIMIT_PRODUCTS);

        $productItems = array_map(
            fn(array $item) => ItemSearchData::fromArray($item, true),
            $productItemsRaw
        );

        return new FullSearchData(
            search: $search,
            products: $productItems,
            categories: [],
            rooms: [],
            recommends: []
        );
    }
}
