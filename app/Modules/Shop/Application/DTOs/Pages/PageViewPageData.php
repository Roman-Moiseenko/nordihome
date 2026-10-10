<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\PageData;
use App\Modules\Shop\Application\DTOs\PageElements\ContentBlockPageData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Domain\Schema\SchemaData;

class PageViewPageData
{
    public function __construct(
        public PageData $page,
        public SeoData     $meta,
        public string $name,
        public string $text = '',
        public string $template = '',
        /** @var ContentBlockPageData[] $blocks */
        public array $blocks = [],
        public SchemaData  $schema = new SchemaData(),
    ) {

    }
}
