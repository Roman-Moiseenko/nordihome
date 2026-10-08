<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

class ViewCompositeProductData
{
    /**
     * @param array<int, array{id: int, code: string, name: string, image: string, quantity: int}> $products
     */
    public function __construct(
        public int $id,
        public array $products,
        public bool $hasModification,
    )
    {
    }

    /**
     * @param array<int, array{id: int, code: string, name: string, image: string, quantity: int}> $products
     */
    public static function create(int $id, array $products, bool $hasModification): self
    {
        return new self(
            id: $id,
            products: $products,
            hasModification: $hasModification,
        );
    }
}
