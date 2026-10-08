<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use Spatie\LaravelData\Data;

/**
 * Данные карточки модификации (Catalog/Modification/Show).
 */
class ModificationViewData extends Data
{
    /**
     * @param ModificationAttributeViewData[] $attributes
     * @param ModificationProductViewData[]   $products
     * @param int[] $usedVariantIds Варианты, используемые товарами модификации.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly array $attributes = [],
        public readonly array $products = [],
        public readonly array $usedVariantIds = [],
    ) {
    }
}
