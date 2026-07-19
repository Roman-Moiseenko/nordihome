<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\Elements\IdNameImageData;
use App\Modules\Shop\Application\DTOs\Entities\AttributeFilterData;

readonly class FilterData
{
    /**
     * @param float $minPrice
     * @param float $maxPrice
     * @param AttributeFilterData[] $attributes
     * @param IdNameImageData[] $brands
     * @param IdNameData[] $tags
     * @param string $sortOrder
     * @param int|null $tagId
     */
    public function __construct(
        public float  $minPrice,
        public float  $maxPrice,
        public array  $attributes,
        public array  $brands,
        public array  $tags,
        public string $sortOrder = '',
        public ?int   $tagId = null,
    )
    {
    }
}
