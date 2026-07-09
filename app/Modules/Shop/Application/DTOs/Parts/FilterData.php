<?php

namespace App\Modules\Shop\Application\DTOs\Parts;

class FilterData
{
    public function __construct(
        public readonly float $minPrice,
        public readonly float $maxPrice,
        /** @var array[] */
        public readonly array $attributes,
        /** @var array[] */
        public readonly array $brands,
        /** @var array[] */
        public readonly array $tags,
    )
    {
    }
}
