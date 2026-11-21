<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Organization;

use App\Modules\Accounting\Domain\Entities\OrganizationEntity;

class OrganizationIndexData
{
    public function __construct(
        public readonly int $id,
        public readonly string $shortName,
        public readonly string $fullName,
        public readonly string $inn,
        public readonly string $chief,
        public readonly string $types,
        public readonly string $phone,
        public readonly string $email,
    ) {
    }

    public static function fromEntity(OrganizationEntity $organization, string $types): self
    {
        return new self(
            id: $organization->id,
            shortName: $organization->shortName,
            fullName: $organization->fullName,
            inn: $organization->inn,
            chief: (string) $organization->chief,
            types: $types,
            phone: $organization->phone->getValue(),
            email: $organization->email?->value ?? '',
        );
    }
}
