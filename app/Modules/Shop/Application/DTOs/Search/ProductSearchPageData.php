<?php

namespace App\Modules\Shop\Application\DTOs\Search;

use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\PageElements\FilterProductsData;
use App\Modules\Shop\Application\DTOs\PageElements\PaginatorData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;


class ProductSearchPageData
{
    public function __construct(

        /** @var ProductCardData[] */
        public array              $products,
        public PaginatorData      $paginator,
        public FilterProductsData $filters,
        public SeoData            $meta,
        public string             $search,

    ) {}
}
