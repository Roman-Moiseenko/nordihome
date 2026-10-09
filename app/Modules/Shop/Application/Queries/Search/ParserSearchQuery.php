<?php

namespace App\Modules\Shop\Application\Queries\Search;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Search\FullSearchData;
use App\Modules\Shop\Application\DTOs\Search\ItemSearchData;

class ParserSearchQuery
{
    public function __construct(
        private ParserProductSearchQueryRepository $searchQueryRepository,

    )
    {
    }
    public function execute(string $search, ClientContext $clientContext): FullSearchData
    {

        $productItemsRaw = $this->searchQueryRepository->getProductBySearch($search, FullSearchData::LIMIT_PRODUCTS);

        $productItems = array_map(
            fn(array $item) => ItemSearchData::fromArray($item, false),
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
