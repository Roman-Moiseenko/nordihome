<?php

namespace App\Modules\Shop\Application\Queries\Search;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Search\FullSearchData;
use App\Modules\Shop\Application\DTOs\Search\ItemSearchData;
use App\Modules\Shop\Application\DTOs\Search\ProductSearchPageData;

class FullSearchQuery
{

    public function execute(string $search, ClientContext $clientContext): FullSearchData
    {



        $productItems =  array_map(
            fn(array $item) => ItemSearchData::fromArray($item),
            $productItemsRaw
        );
        $categoryItems =  array_map(
            fn(array $item) => ItemSearchData::fromArray($item),
            $categoryItemsRaw
        );

        $roomItems =  array_map(
            fn(array $item) => ItemSearchData::fromArray($item),
            $roomItemsRaw
        );

        return new FullSearchData(
            products: $productItems,
            categories: $categoryItems,
            rooms: $roomItems,
            recommends: [],
            search: $search
        );
    }
}
