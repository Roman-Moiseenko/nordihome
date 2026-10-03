<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\PriceOutbox;

use App\Modules\Accounting\Domain\Entities\PriceOutboxEntity;
use Spatie\LaravelData\Data;

class PriceOutboxViewData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly int $retail,
        public readonly float $sellIkea,
        public readonly int $bulk,
    ) {}

    public static function fromEntity(PriceOutboxEntity $entity): self
    {
        return new self(
            code: $entity->code,
            retail: $entity->retail,
            sellIkea: $entity->sellIkea,
            bulk: $entity->bulk,
        );
    }
}
