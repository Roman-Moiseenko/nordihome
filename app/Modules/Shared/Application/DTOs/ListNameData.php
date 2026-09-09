<?php

namespace App\Modules\Shared\Application\DTOs;

use Spatie\LaravelData\Data;

class ListNameData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public function fromEntity(object $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
        );
    }
}
