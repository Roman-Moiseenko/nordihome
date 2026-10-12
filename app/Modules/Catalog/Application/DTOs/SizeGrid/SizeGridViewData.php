<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\SizeGrid;

use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Entities\SizeGridValueEntity;
use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;
use Spatie\LaravelData\Data;

/**
 * DTO для страницы просмотра/редактирования размерной сетки (Catalog/SizeGrid/Show).
 */
class SizeGridViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        /** @var SizeGridDimensionData[] */
        public readonly array $dimensions,
        /** @var SizeGridValueData[] */
        public readonly array $values,
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
            values: array_map(
                fn(SizeGridValueEntity $value) => SizeGridValueData::fromEntity($value),
                $grid->values,
            ),
        );
    }
}
