<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use App\Modules\Catalog\Application\DTOs\Product\ProductSearchData;
use App\Modules\Shared\Application\DTOs\ListNameData;
use Spatie\LaravelData\Data;

/**
 * Результат searchCreate: найденные товары (без уже занятых в модификациях)
 * и атрибуты-варианты, доступные для осей модификации.
 */
class ModificationCreateSearchData extends Data
{
    /**
     * @param ProductSearchData[] $products
     * @param ListNameData[] $attributes
     */
    public function __construct(
        public readonly array $products = [],
        public readonly array $attributes = [],
    ) {
    }
}
