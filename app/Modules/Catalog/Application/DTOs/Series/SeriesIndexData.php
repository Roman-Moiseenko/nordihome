<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Series;

use App\Modules\Catalog\Domain\Entities\SeriesEntity;

class SeriesIndexData
{
    public function __construct(
        public int $id,
        public string $name,
        public int $quantity,
    ) {}

    public static function fromEntity(SeriesEntity $series, int $quantity): self
    {
        return new self(
            id: $series->id,
            name: $series->name,
            quantity: $quantity,
        );
    }
}
