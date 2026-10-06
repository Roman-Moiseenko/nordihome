<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class AttributeCreateData extends Data
{
    /**
     * @param int[] $categories
     */
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType]
        public readonly string $type,
        #[MapInputName('group_id'), Required, Numeric]
        public readonly int $groupId,
        public readonly array $categories = [],
    )
    {
    }
}
