<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\DTOs;

use Spatie\LaravelData\Data;

class JobPhotoLoadData extends Data
{
    public function __construct(
        public readonly int $imageableId,
        public readonly string $modelType,
        public readonly string $type,
        public readonly string $url,
        public readonly ?int $sort = null,
        public readonly ?string $alt = null,
    )
    {
    }
}
