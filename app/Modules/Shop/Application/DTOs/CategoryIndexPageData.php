<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs;

use App\Modules\Shop\Application\DTOs\Parts\SeoData;

readonly class CategoryIndexPageData
{

    public function __construct(
        public SeoData $meta,
        /** @var CategoryTreeClientData[] $categories */
        public array   $categories,
    )
    {
    }
}
