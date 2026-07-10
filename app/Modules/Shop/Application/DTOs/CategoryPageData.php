<?php

namespace App\Modules\Shop\Application\DTOs;

use App\Modules\Shop\Application\DTOs\Parts\CategoryInfo;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\PaginatorData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCardData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;

class CategoryPageData
{
    public function __construct(
        public CategoryInfo $category,
        /** @var CategoryInfo[] */
        public array $children,
        /** @var ProductCardData[] */
        public array $products,
        public PaginatorData $paginator,
        public FilterData $filters,
        public SeoData $meta,
    ) {}
}
