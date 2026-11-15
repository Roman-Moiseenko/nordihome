<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoCommonImage;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class AvitoCommonImageSortData extends Data
{
    /**
     * @param int[] $ids
     */
    public function __construct(
        #[Required]
        public readonly array $ids,
    ) {}
}
