<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use App\Modules\Catalog\Domain\Entities\ProductEntity;

class ViewManagementProductData
{
    /**
     * @param array<int, array{id: int, name: string, cell: string}> $storages
     * @param array{min: int, max: int|null, buy: bool} $balance
     */
    public function __construct(
        public int $id,
        public bool $published,
        public bool $notSale,
        public bool $priority,
        public bool $priceReduced,
        public bool $hidePrice,
        public bool $preOrder,
        public bool $onlyOnOrder,
        public array $storages,
        public array $balance,
        public bool $hasModification,
    )
    {
    }

    /**
     * @param array<int, array{id: int, name: string, cell: string}> $storages
     * @param array{min: int, max: int|null, buy: bool} $balance
     */
    public static function fromEntity(
        ProductEntity $entity,
        array $storages,
        array $balance,
    ): self {
        return new self(
            id: $entity->id,
            published: $entity->published,
            notSale: $entity->notSale,
            priority: $entity->priority,
            priceReduced: $entity->priceReduced,
            hidePrice: $entity->hidePrice,
            preOrder: $entity->preOrder,
            onlyOnOrder: $entity->onlyOnOrder,
            storages: $storages,
            balance: $balance,
            hasModification: $entity->hasModification,
        );
    }
}
