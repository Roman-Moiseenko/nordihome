<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

use App\Modules\Catalog\Domain\Entities\ProductEntity;

class ViewCommonProductData
{
    /**
     * @param int[] $categories ID доп. категорий товара
     * @param int[] $rooms      ID комнат товара
     */
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
        public ?int $countryId,
        public ?int $markingTypeId,
        public ?int $measuringId,
        public bool $fractional,
        public bool $hasModification,
    )
    {
    }

    /**
     * @param int[] $categoryIds
     * @param int[] $roomIds
     */
    public static function fromEntity(ProductEntity $entity, array $categoryIds = [], array $roomIds = []): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            namePrint: $entity->namePrint,
            slug: (string) $entity->slug,
            code: (string) $entity->code,
            comment: $entity->comment,
            categoryId: $entity->mainCategoryId,
            categories: array_values(array_map('intval', $categoryIds)),
            rooms: array_values(array_map('intval', $roomIds)),
            brandId: $entity->brandId,
            countryId: $entity->countryId,
            markingTypeId: $entity->markingTypeId,
            measuringId: $entity->measuringId,
            fractional: $entity->fractional,
            hasModification: $entity->hasModification,
        );
    }
}
