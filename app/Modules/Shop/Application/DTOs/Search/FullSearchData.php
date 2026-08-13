<?php

namespace App\Modules\Shop\Application\DTOs\Search;

use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;

class FullSearchData
{
    public const int LIMIT_PRODUCTS = 10;
    public const int LIMIT_CATEGORIES = 3;
    public const int LIMIT_ROOMS = 3;

    public function __construct(
        public string $search,

        /** @var ItemSearchData[] */
        public array  $products,
        /** @var ItemSearchData[] */
        public array  $categories = [],
        /** @var ItemSearchData[] */
        public array  $rooms = [],
        /** @var ItemSearchData[] */
        public array  $recommends = [],
    )
    {
    }
}
