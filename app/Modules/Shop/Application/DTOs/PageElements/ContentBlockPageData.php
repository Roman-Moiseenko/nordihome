<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

use App\Modules\Shop\Application\DTOs\Elements\WidgetPageData;

class ContentBlockPageData
{
    public function __construct(
        public string $section,
        public WidgetPageData $widget,
    ) {

    }
}
