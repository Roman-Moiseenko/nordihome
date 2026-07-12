<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\IkeaTreeClientData;
use App\Modules\Shop\Application\DTOs\PageElements\SchemaData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

readonly class IkeaIndexPageData
{
    public function __construct(
        public SeoData    $meta,
        public SchemaData $schema,
        /** @var IkeaTreeClientData[] $categories */
        public array      $categories,
    )
    {

    }
}
