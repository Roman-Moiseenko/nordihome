<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use Spatie\LaravelData\Data;

/**
 * Атрибут-ось модификации вместе со всеми вариантами (Catalog/Modification/Show).
 */
class ModificationAttributeViewData extends Data
{
    /**
     * @param ModificationVariantViewData[] $variants
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly array $variants = [],
    ) {
    }
}
