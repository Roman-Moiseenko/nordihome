<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use App\Modules\Catalog\Application\DTOs\Product\ProductSearchData;
use Spatie\LaravelData\Data;

/**
 * Результат поиска товаров для добавления в модификацию (Show).
 */
class ModificationProductSearchData extends Data
{
    /**
     * @param ProductSearchData[] $products
     */
    public function __construct(
        public readonly array $products = [],
    ) {
    }
}
