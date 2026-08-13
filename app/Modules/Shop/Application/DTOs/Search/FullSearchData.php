<?php

namespace App\Modules\Shop\Application\DTOs\Search;

use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;

class FullSearchData
{
    public function __construct(

        /** @var ItemSearchData[] */
        public array  $products,
        /** @var ItemSearchData[] */
        public array  $categories = [],
        /** @var ItemSearchData[] */
        public array  $rooms = [],
        /** @var ItemSearchData[] */
        public array  $recommends = [],
        public string $search,

    )
    {
    }
}
