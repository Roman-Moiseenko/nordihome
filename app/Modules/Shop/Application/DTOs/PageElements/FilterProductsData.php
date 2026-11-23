<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\Elements\IdNameImageData;
use App\Modules\Shop\Application\DTOs\Entities\AttributeFilterData;

readonly class FilterProductsData
{
    /**
     * @param MinMaxData $price
     * @param MinMaxData $width
     * @param MinMaxData $height
     * @param MinMaxData $depth
     * @param AttributeFilterData[] $attributes
     * @param IdNameImageData[] $brands
     * @param IdNameData[] $tags
     * @param string $sortOrder
     * @param int|null $tagId
     */
    public function __construct(
        public MinMaxData $price,
        public MinMaxData $width,
        public MinMaxData $height,
        public MinMaxData $depth,
        public array       $attributes,
        public array       $brands,
        public array       $tags,
        public string      $sortOrder = '',
        public ?int        $tagId = null,
    )
    {
    }
}
