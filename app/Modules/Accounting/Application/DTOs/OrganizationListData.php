<?php

namespace App\Modules\Accounting\Application\DTOs;

use Spatie\LaravelData\Data;

class OrganizationListData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $shortName,
        public readonly string $INN,
    ) {

    }
}
