<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\IkeaProductData;
use App\Modules\Shop\Application\DTOs\PageElements\SchemaData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

class IkeaProductPageData
{
    public function __construct(
        public array $categories,
        public IkeaProductData $product,
        public SeoData    $meta,
        public SchemaData $schema,
        public int $currentId,
    )
    {

    }
}
