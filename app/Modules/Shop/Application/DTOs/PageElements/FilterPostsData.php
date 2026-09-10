<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\Elements\IdNameImageData;
use App\Modules\Shop\Application\DTOs\Entities\AttributeFilterData;

readonly class FilterPostsData
{
    /**
     * @param IdNameData[] $labels
     * @param string $sortOrder
     * @param int|null $labelId
     */
    public function __construct(

        public array  $labels,
        public string $sortOrder = '',
        public ?int   $labelId = null,
    )
    {
    }
}
