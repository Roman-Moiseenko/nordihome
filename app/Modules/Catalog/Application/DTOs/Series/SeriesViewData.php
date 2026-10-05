<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Series;

use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use Spatie\LaravelData\Data;

class SeriesViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $nameRu,
        public readonly array $products = [],
    )
    {
    }

    public static function fromEntity(SeriesEntity $series, array $products = []): self
    {
        return new self(
            id: $series->id,
            name: $series->name,
            nameRu: $series->nameRu,
            products: $products,
        );
    }
}
