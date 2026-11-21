<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

final class TraderOrganizationEntity
{
    public function __construct(
        public int $organizationId,
        public string $shortName = '',
        public string $inn = '',
        public bool $default = false,
    ) {
    }
}
