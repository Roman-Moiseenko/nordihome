<?php

namespace App\Modules\Shared\Application\DTOs;

use Spatie\LaravelData\Data;

class ListCodeData extends Data
{
    public function __construct(
        public int    $id,
        public string $code,
    ) {}

    public function fromEntity(object $entity): self
    {
        return new self(
            id: $entity->id,
            code: $entity->name,
        );
    }
}
