<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs\Stock;

use App\Modules\Accounting\Domain\Entities\StockItemEntity;

readonly class StockIndexData
{
    public function __construct(
        public int $id,
        public string $code,
        public string $category,
        public int $quantity,
        public int $reserve,
    ) {
    }

    public static function fromEntity(StockItemEntity $item): self
    {
        return new self(
            id: $item->id,
            code: $item->code,
            category: $item->categoryName,
            quantity: $item->quantity,
            reserve: $item->reserve,
        );
    }
}
