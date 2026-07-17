<?php

namespace App\Modules\Shop\Application\DTOs\Elements;

class WidgetPageData
{

    public function __construct(
        public string $category,
        public string $slug,
        public array $params,
    ) {

    }
}
