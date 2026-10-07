<?php

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Domain\Interfaces\AttributeProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\ModificationAttributes;
use App\Modules\Catalog\Domain\ValueObjects\ModificationValues;

final readonly class ModificationValuesResolver
{
    public function __construct(
        private AttributeProductRepositoryInterface $attributeProductRepository
    ) {
    }

    public function forProduct(int $productId, array $attributes): ModificationValues
    {
        $map = [];

        foreach ($attributes as $attributeId) {
            $raw = $this->attributeProductRepository->valueOf($productId, $attributeId);

            if ($raw === null) {
                continue; // товар не имеет значения по этой оси — допустимо
            }

            // variant хранится как [variant_id], берём первый.
            $map[$attributeId] = is_array($raw)
                ? (int) ($raw[0] ?? 0)
                : (int) $raw;
        }

        return new ModificationValues($map);
    }
}
