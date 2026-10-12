<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\SizeGrid;

use App\Modules\Catalog\Application\DTOs\SizeGrid\SizeGridDimensionData;
use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;

/**
 * Возвращает доступные измерения размерной сетки (для useCatalogStore).
 */
readonly class ListSizeGridDimensionsQuery
{
    /**
     * @return SizeGridDimensionData[]
     */
    public function execute(): array
    {
        return array_map(
            fn(SizeGridDimension $dimension) => SizeGridDimensionData::fromVO($dimension),
            SizeGridDimension::available(),
        );
    }
}
