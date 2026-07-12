<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomSecondData;
use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\PageElements\FilterData;
use App\Modules\Shop\Application\DTOs\PageElements\PaginatorData;
use App\Modules\Shop\Application\DTOs\PageElements\SchemaData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;


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
        public SchemaData           $schema,
    ) {}
}
