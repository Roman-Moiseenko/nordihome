<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\AttributeGroup;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class AttributeGroupSortData extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public readonly int $id,

        #[Required, IntegerType]
        public readonly int $sort,
    )
    {
    }
}
