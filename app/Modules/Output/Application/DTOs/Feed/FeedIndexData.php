<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Feed;

use App\Modules\Output\Domain\Entities\FeedEntity;
use Spatie\LaravelData\Data;

class FeedIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $active,
    ) {}

    public static function fromEntity(FeedEntity $feed): self
    {
        return new self(
            id: $feed->id,
            name: $feed->name,
            active: (bool) $feed->active,
        );
    }
}
