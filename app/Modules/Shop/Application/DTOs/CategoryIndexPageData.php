<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs;

use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\Parts\SeoData;

readonly class CategoryIndexPageData
{

    public function __construct(
        public SeoData $meta,
        /** @var CategoryRoomData[] $categories */
        public array   $categories,
    )
    {
    }
}
