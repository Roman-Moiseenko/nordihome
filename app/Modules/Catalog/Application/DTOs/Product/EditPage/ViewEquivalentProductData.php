<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

class ViewEquivalentProductData
{
    /**
     * @param array<int, array{id: int, name: string}> $equivalents
     * @param array{id: int, name: string, products: array<int, array{id: int, code: string, name: string, image: string}>}|null $currentEquivalent
     */
    public function __construct(
        public int $id,
        public array $equivalents,
        public ?int $currentEquivalentId,
        public ?array $currentEquivalent,
        public bool $hasModification,
    )
    {
    }

    /**
     * @param array<int, array{id: int, name: string}> $equivalents
     * @param array{id: int, name: string, products: array<int, array{id: int, code: string, name: string, image: string}>}|null $currentEquivalent
     */
    public static function create(
        int $id,
        array $equivalents,
        ?int $currentEquivalentId,
        ?array $currentEquivalent,
        bool $hasModification,
    ): self {
        return new self(
            id: $id,
            equivalents: $equivalents,
            currentEquivalentId: $currentEquivalentId,
            currentEquivalent: $currentEquivalent,
            hasModification: $hasModification,
        );
    }
}
