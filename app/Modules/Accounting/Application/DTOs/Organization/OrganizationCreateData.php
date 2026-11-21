<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Organization;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class OrganizationCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Min(10), Max(12)]
        public readonly string $inn,
        #[Nullable, StringType]
        public readonly ?string $bik = null,
        #[Nullable, StringType]
        public readonly ?string $account = null,
        #[Nullable, StringType]
        public readonly ?string $name = null,
        #[Nullable, StringType]
        public readonly ?string $bank = null,
        #[Nullable, BooleanType]
        public readonly ?bool $foreign = false,
    ) {
    }
}
