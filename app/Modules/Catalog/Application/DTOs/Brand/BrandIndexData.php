<?php

namespace App\Modules\Catalog\Application\DTOs\Brand;

use App\Modules\Catalog\Domain\Entities\BrandEntity;

class BrandIndexData
{
    public function __construct(
        public int $id,
        public string $name,
        public int $quantity,
        public string $parserClass,
    ) {}


    public static function fromEntity(BrandEntity $brand, int $quantity): self
    {
        return new self(
            id: $brand->id,
            name: $brand->name,
            quantity: $quantity,
            parserClass: $brand->parserClass,
        );
    }
}
