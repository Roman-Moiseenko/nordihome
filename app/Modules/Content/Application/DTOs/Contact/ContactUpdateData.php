<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Contact;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ContactUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name = null,
        #[Nullable, StringType]
        public readonly ?string $url = null,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug = null,
        #[Nullable, StringType]
        public readonly ?string $icon = null,
        #[Nullable, StringType]
        public readonly ?string $color = null,
        #[Nullable, StringType]
        public readonly ?string $svg = null,
        #[Nullable, StringType]
        public readonly ?string $channel = null,
    ) {
    }
}
