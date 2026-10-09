<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use Spatie\LaravelData\Data;

/**
 * Вариант атрибута модификации (Catalog/Modification/Show).
 */
class ModificationVariantViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }
}
