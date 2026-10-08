<?php

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

class ViewCommonProductData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $namePrint,
        public string $slug,
        public string $code,
        public string $comment,
        public int $categoryId,
        public array $categories,
        public array $rooms,
        public int $brandId,
        public int $countryId,
        public int $markingTypeId,
        public int $measuringId,
        public bool $fractional,
        public bool $isModification,
    )
    {

    }
}
