<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs;

use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RoomTreeData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        public readonly string $name,
        public readonly int $depth,
        /** @var RoomTreeData[] */
        public readonly array $children = [],
    )
    {
    }
}
