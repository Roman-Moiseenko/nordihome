<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Organization;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class OrganizationUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public readonly ?string $fullName = null,
        #[Nullable, StringType]
        public readonly ?string $shortName = null,
        #[Nullable, StringType]
        public readonly ?string $email = null,
        #[Nullable, StringType]
        public readonly ?string $phone = null,
        #[Nullable, StringType]
        public readonly ?string $post = null,
        #[Nullable, StringType]
        public readonly ?string $chief = null,
        #[Nullable, StringType]
        public readonly ?string $payAccount = null,
        #[Nullable, StringType]
        public readonly ?string $bik = null,
        #[Nullable, StringType]
        public readonly ?string $bank = null,
        #[Nullable]
        public readonly ?array $address = null,
        #[Nullable, Numeric]
        public readonly ?int $vatId = null,
        #[Nullable, BooleanType]
        public readonly ?bool $vatAll = false,
    ) {
    }
}
