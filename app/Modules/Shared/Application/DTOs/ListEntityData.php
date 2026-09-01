<?php

namespace App\Modules\Shared\Application\DTOs;

use Spatie\LaravelData\Data;

class ListEntityData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $published,
    ) {}
}
