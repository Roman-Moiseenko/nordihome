<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Trader;

use App\Modules\Accounting\Domain\Entities\TraderEntity;
use Spatie\LaravelData\Data;

class TraderViewData extends Data
{
    /**
     * @param array<int, array<string, mixed>> $organizations
     * @param array<int, array<string, mixed>> $contacts
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $url,
        public readonly bool $default,
        public readonly bool $active,
        public readonly ?string $logo,
        public readonly array $organizations,
        public readonly array $contacts,
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $contacts
     */
    public static function fromEntity(TraderEntity $trader, array $contacts = []): self
    {
        return new self(
            id: $trader->id,
            name: $trader->name,
            description: $trader->description,
            url: $trader->url,
            default: $trader->isDefault(),
            active: $trader->active,
            logo: $trader->logo?->getUrl(),
            organizations: array_map(
                fn($organization) => [
                    'id' => $organization->organizationId,
                    'short_name' => $organization->shortName,
                    'inn' => $organization->inn,
                    'default' => $organization->default,
                ],
                $trader->organizations
            ),
            contacts: $contacts,
        );
    }
}
