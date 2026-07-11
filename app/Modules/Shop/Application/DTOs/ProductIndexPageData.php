<?php

namespace App\Modules\Shop\Application\DTOs;

use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomMainData;
use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomSecondData;
use App\Modules\Shop\Application\DTOs\Parts\ChildrenData;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\PaginatorData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCardData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;
use App\Modules\Shop\Application\DTOs\Parts\UrlData;

class ProductIndexPageData
{
    public function __construct(
        public CategoryRoomMainData $mainInfo,
        public CategoryRoomSecondData $secondInfo,
        /** @var ProductCardData[] */
        public array                $products,
        public PaginatorData        $paginator,
        public FilterData           $filters,
        public SeoData              $meta,
    ) {}
}
