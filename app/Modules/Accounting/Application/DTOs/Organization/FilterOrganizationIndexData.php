<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Organization;

use Spatie\LaravelData\Data;

class FilterOrganizationIndexData extends Data
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
    ) {
    }
}
