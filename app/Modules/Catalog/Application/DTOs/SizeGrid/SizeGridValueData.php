<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\SizeGrid;

use App\Modules\Catalog\Domain\Entities\SizeGridValueEntity;
use Spatie\LaravelData\Data;

/**
 * Значение размерной сетки (для фронтенда).
 */
class SizeGridValueData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $dimensionCode,
        public readonly string $value,
        public readonly ?string $label,
        public readonly int $sortOrder,
        public readonly bool $isActive,
    ) {
    }

    public static function fromEntity(SizeGridValueEntity $value): self
    {
        return new self(
            id: $value->id,
            dimensionCode: $value->dimensionCode,
            value: $value->value,
            label: $value->label,
            sortOrder: $value->sortOrder,
            isActive: $value->isActive,
        );
    }
}
