<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\SizeGrid;

use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;
use Spatie\LaravelData\Data;

/**
 * DTO строки списка размерных сеток (Catalog/SizeGrid/Index).
 */
class SizeGridIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        /** @var SizeGridDimensionData[] */
        public readonly array $dimensions,
        public readonly int $valuesCount,
    ) {
    }

    public static function fromEntity(SizeGridEntity $grid): self
    {
        return new self(
            id: $grid->id,
            name: $grid->name,
            dimensions: array_map(
                fn(SizeGridDimension $dimension) => SizeGridDimensionData::fromVO($dimension),
                $grid->dimensions,
            ),
            valuesCount: count($grid->values),
        );
    }
}
