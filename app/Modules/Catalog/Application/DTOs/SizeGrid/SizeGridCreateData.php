<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\SizeGrid;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class SizeGridCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,

        /** @var array<int, string|array<string, mixed>> Коды измерений (или [{code, name}]) */
        public readonly array $dimensions = [],
    ) {
    }
}
