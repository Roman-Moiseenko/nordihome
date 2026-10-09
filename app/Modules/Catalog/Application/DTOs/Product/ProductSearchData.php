<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
use Spatie\LaravelData\Data;

/**
 * DTO результата поиска товара (выпадающие списки поиска).
 */
class ProductSearchData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
    ) {
    }

    public static function fromEntity(ProductEntity $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            code: (string) $entity->code,
        );
    }
}
