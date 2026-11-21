<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Organization;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class OrganizationContactData extends Data
{
    public function __construct(
        #[Nullable, Numeric]
        public readonly ?int $id = null,
        #[Nullable, StringType]
        public readonly ?string $fullname = null,
        #[Nullable, StringType]
        public readonly ?string $post = null,
        #[Nullable, StringType]
        public readonly ?string $phone = null,
        #[Nullable, StringType]
        public readonly ?string $email = null,
    ) {
    }
}
