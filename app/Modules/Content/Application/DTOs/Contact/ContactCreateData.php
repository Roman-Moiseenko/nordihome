<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Contact;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ContactCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Required, StringType]
        public readonly string $url,
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
