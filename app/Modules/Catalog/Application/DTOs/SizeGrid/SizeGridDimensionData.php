<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\SizeGrid;

use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;
use Spatie\LaravelData\Data;

/**
 * Измерение размерной сетки (для фронтенда).
 */
class SizeGridDimensionData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
    ) {
    }

    public static function fromVO(SizeGridDimension $dimension): self
    {
        return new self(
            code: $dimension->code,
            name: $dimension->name,
        );
    }
}
