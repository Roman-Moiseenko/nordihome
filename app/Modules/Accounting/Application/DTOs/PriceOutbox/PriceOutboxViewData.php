<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\PriceOutbox;

use App\Modules\Accounting\Domain\Entities\PriceOutboxEntity;
use Spatie\LaravelData\Data;

class PriceOutboxViewData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly int $price,
        public readonly float $priceIkea,
    ) {}

    public static function fromEntity(PriceOutboxEntity $entity): self
    {
        return new self(
            code: $entity->code,
            price: $entity->price,
            priceIkea: $entity->priceIkea,
        );
    }
}
