<?php

namespace App\Modules\Shared\Application\DTOs\Photo;

use Spatie\LaravelData\Data;

class PhotoByEntityResultItemData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
    )
    {
    }
}
