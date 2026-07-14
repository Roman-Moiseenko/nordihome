<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\IkeaCategoryMainData;
use App\Modules\Shop\Application\DTOs\IkeaTreeClientData;
use App\Modules\Shop\Application\DTOs\PageElements\PaginatorData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Domain\Schema\SchemaData;

class IkeaViewPageData
{
    public function __construct(

        public IkeaCategoryMainData $category,
        /** @var IkeaTreeClientData[] $categories */
        public array $categories,
        public array $products,
        public PaginatorData        $paginator,
        public SeoData    $meta,
        public SchemaData $schema,
    )
    {

    }
}
