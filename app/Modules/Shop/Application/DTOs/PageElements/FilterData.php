<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

use App\Modules\Shop\Application\DTOs\Elements\IdNameData;

readonly class FilterData
{
    public function __construct(
        public float  $minPrice,
        public float  $maxPrice,
        /** @var array[] */
        public array  $attributes,
        /** @var IdNameData[] */
        public array  $brands,
        /** @var IdNameData[] */
        public array  $tags,
        public string $sortOrder = '',
        public ?int $tagId = null
    )
    {
    }
}
