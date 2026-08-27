<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\DTOs\Promotion;

use App\Modules\Discount\Domain\Entities\PromotionEntity;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use Spatie\LaravelData\Data;

/**
 * DTO для списка акций (index).
 */
class PromotionIndexData extends Data
{
    public function __construct(
        public readonly int    $id,
        public readonly string $image,
        public readonly string $name,
        public readonly ?string $start,
        public readonly ?string $finish,
        public readonly string $status,
        public readonly int    $quantity,
    )
    {
    }

    public static function fromEntity(PromotionEntity $promotion, int $quantity, string $image): self
    {
        return new self(
            id: $promotion->id ?? 0,
            image: $image,
            name: $promotion->name,
            start: $promotion->startAt?->format('d.m.Y') ?? null,
            finish: $promotion->finishAt?->format('d.m.Y') ?? null,
            status: $promotion->status?->value() ?? PromotionStatus::default()->value(),
            quantity: $quantity,
        );
    }
}
