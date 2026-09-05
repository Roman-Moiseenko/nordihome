<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

use App\Modules\Lead\Domain\Entities\LeadItemEntity;
use Spatie\LaravelData\Data;

class LeadCommentData extends Data
{
    public function __construct(
        public readonly string $comment,
        public readonly string $createdAt,
        public readonly ?string $finishedAt,
    )
    {

    }
    public static function fromEntity(LeadItemEntity $entity): self
    {
        return new self(
            comment: $entity->comment,
            createdAt: $entity->createdAt->format('Y-m-d H:i:s'),
            finishedAt: $entity->finishedAt?->format('Y-m-d') ?? null,
        );
    }
}
