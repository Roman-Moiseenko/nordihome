<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomSecondData;
use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\PageElements\ContentBlockPageData;
use App\Modules\Shop\Application\DTOs\PageElements\FilterData;
use App\Modules\Shop\Application\DTOs\PageElements\PaginatorData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Domain\Schema\SchemaData;


class ProductSearchPageData
{
    public function __construct(

        /** @var ProductCardData[] */
        public array                $products,
        public PaginatorData        $paginator,
        public FilterData           $filters,
        public SeoData              $meta,
        public string               $search,

    ) {}
}
