<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Organization;

use App\Modules\Auth\Domain\ValueObjects\Address;

final readonly class OrganizationSchemaData
{
    /**
     * @param string[] $sameAs
     * @param string[] $phones
     */
    public function __construct(
        public string $name,
        public Address $address,
        public string $logoUrl = '',
        public int $logoWidth = 0,
        public int $logoHeight = 0,
        public string $logoCaption = '',
        public array $sameAs = [],
        public array $phones = [],
    ) {
    }
}
