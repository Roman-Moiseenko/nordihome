<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\PageElements\PaginatorData;
use App\Modules\Shop\Application\DTOs\PageElements\SchemaData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

class ProductViewPageData
{
    public function __construct(
        /** @var array[] */
        public readonly array         $products,
        /** @var PaginatorData */
        public readonly PaginatorData $paginator,
        public SeoData                $seo,
        public SchemaData             $schema,
    )
    {
    }
}
