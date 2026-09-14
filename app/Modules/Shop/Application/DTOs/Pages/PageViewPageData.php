<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\PageElements\ContentBlockPageData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

class PageViewPageData
{
    public function __construct(
        public SeoData     $meta,
        public string $name,
        public string $text = '',
        public string $template = '',
        /** @var ContentBlockPageData[] $blocks */
        public array $blocks = [], //На будущее

    ) {

    }
}
