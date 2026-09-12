<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\DTOs\Action;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class TrackActionData extends Data
{
    public function __construct(
        #[Required, StringType, Max(50)]
        public readonly string $actionType,
        #[Nullable, StringType, Max(50)]
        public readonly ?string $entityType,
        #[Nullable, Numeric]
        public readonly ?int $entityId,
        #[Nullable, Numeric]
        public readonly ?int $pageViewId,
        #[Nullable]
        public readonly ?array $payload,
    ) {}
}
