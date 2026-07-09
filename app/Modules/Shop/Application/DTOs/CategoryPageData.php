<?php

namespace App\Modules\Shop\Application\DTOs;

use App\Modules\Shop\Application\DTOs\Parts\CategoryInfo;
use App\Modules\Shop\Application\DTOs\Parts\FilterData;
use App\Modules\Shop\Application\DTOs\Parts\ProductCardPage;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;

class CategoryPageData
{
    public function __construct(
        public CategoryInfo $category,         // id, name, slug, breadcrumbs
        /** @var CategoryInfo[] */
        public array $children,                // массив CategoryInfo
        public ProductCardPage $products,      // пагинированный список товаров
        public FilterData $filters,            // атрибуты, бренды, мин/макс цена, теги
        public SeoData $meta,
        // ...
    ) {}
}
