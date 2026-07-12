<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\PageElements\SchemaData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

class IkeaProductPageData
{
    public function __construct(
        public SeoData    $meta,
        public SchemaData $schema,
    )
    {

    }
}
