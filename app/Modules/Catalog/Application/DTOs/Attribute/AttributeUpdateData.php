<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class AttributeUpdateData extends Data
{
    /**
     * @param int[] $categories
     * @param array<int, array{id: ?int, name: ?string}>|null $variants
     */
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType]
        public readonly string $type,
        #[MapInputName('group_id'), Required, Numeric]
        public readonly int $groupId,
        #[BooleanType]
        public readonly bool $multiple = false,
        #[BooleanType]
        public readonly bool $filter = true,
        #[MapInputName('show_in'), BooleanType]
        public readonly bool $showIn = true,
        #[Nullable, StringType]
        public readonly ?string $sameAs = null,
        public readonly array $categories = [],
        public readonly ?array $variants = null,
    )
    {
    }
}
