<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Trader;

use App\Modules\Accounting\Domain\Entities\TraderEntity;

class TraderIndexData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $organization,
        public readonly bool $default,
    ) {
    }

    public static function fromEntity(TraderEntity $trader): self
    {
        $defaultOrganization = null;
        foreach ($trader->organizations as $organization) {
            if ($organization->default) {
                $defaultOrganization = $organization->shortName;
                break;
            }
        }

        return new self(
            id: $trader->id,
            name: $trader->name,
            organization: $defaultOrganization,
            default: $trader->isDefault(),
        );
    }
}
