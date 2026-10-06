<?php

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use Spatie\LaravelData\Data;

class ListAttributeTypeData extends Data
{
    public function __construct(
        public readonly string $value,
        public readonly string $label,
        public readonly ?bool $isVariant = false,
    )
    {
    }

}
