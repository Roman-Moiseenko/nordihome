<?php

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\IdNameImageData;

readonly class AttributeFilterData
{
    /**
     * @param int $id
     * @param string $name
     * @param bool $isBool
     * @param bool $isNumeric
     * @param float|null $min
     * @param float|null $max
     * @param bool $isVariant
     * @param IdNameImageData[] $variants
     */
    public function __construct(
        public int    $id,
        public string $name,
        public bool  $isBool = false,
        public bool  $isNumeric = false,
        public bool  $isVariant = false,
        public array  $variants = [],
        public ?float $min = null,
        public ?float $max = null,

    )
    {
    }
}
