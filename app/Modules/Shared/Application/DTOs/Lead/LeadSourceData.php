<?php

namespace App\Modules\Shared\Application\DTOs\Lead;

readonly class LeadSourceData
{

    public function __construct(
        public int $id,
        public string $able,
        public array $data
    )
    {

    }

    public static function fromEntity(object $entity): self
    {
        return new self(
            id: $entity->id,
            able: $entity->able,
            data: $entity->data,
        );
    }
}
