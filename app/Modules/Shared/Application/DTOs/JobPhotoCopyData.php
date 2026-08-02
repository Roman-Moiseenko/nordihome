<?php

namespace App\Modules\Shared\Application\DTOs;

class JobPhotoCopyData
{
    public function __construct(
        public readonly int $imageableId,
        public readonly string $modelType,
        public readonly string $type,
        public readonly int $copyId,
        public readonly ?int $sort = null,
        public readonly ?string $alt = null,
    )
    {
    }
}
