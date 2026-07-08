<?php

namespace App\Modules\Auth\Application\DTOs\Staff;

use App\Modules\Auth\Domain\Entities\StaffEntity;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class StaffCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $lastName,
        #[Required, StringType, Max(255)]
        public readonly string $firstName,
        #[Required, ArrayType]
        public readonly array $positions,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $middleName = null,

        #[Nullable, StringType, Max(255)]
        public readonly ?string $workPhone = null,
        #[Nullable, Email, Max(255)]
        public readonly ?string $workEmail = null,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $password = null,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $telegramChatId = null,
    ) {}

    public static function fromEntity(StaffEntity $staff): static
    {
        return new self(
            lastName: $staff->fullName->getLastName(),
            firstName: $staff->fullName->getFirstName(),
            positions: $staff->positions->toArrayOfStrings(),
            middleName: $staff->fullName->getMiddleName(),
            workPhone: $staff->workPhone->getValue(),
            workEmail: $staff->workEmail->value,
            telegramChatId: $staff->telegramChatId,
        );
    }

}
