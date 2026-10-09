<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\Actions\Modification\GetModificationByProductQuery;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateRelatedProductData;
use App\Modules\Catalog\Infrastructure\Models\Product;

readonly class SetRelatedProductUseCase
{
    public function __construct(
        private GetModificationByProductQuery $getModificationByProductQuery,
    ) {
    }

    public function execute(int $id, UpdateRelatedProductData $dto): void
    {
        $productId = $dto->productId;
        if ($productId === null) {
            return;
        }

        $applyToModification = $dto->modification ?? false;

        foreach ($this->resolveTargets($id, $applyToModification) as $targetId => $variantsLine) {
            $product = Product::findOrFail($targetId);

            if ($dto->action === 'remove') {
                $product->related()->detach($productId);
            } elseif (!$product->isRelated($productId)) {
                $product->related()->attach($productId);
            }
        }
    }

    /**
     * @return array<int, string> productId => строка вариантов (" 1 2")
     */
    private function resolveTargets(int $id, bool $applyToModification): array
    {
        if (!$applyToModification) {
            return [$id => ''];
        }

        $modification = $this->getModificationByProductQuery->execute($id);

        if ($modification === null) {
            return [$id => ''];
        }

        $items = [];
        foreach ($modification->products as $product) {
            $items[$product->productId] = $product->values;
        }

        if (!isset($items[$id])) {
            $items[$id] = [];
        }

        $count = count($items);

        $targets = [];
        foreach ($items as $productId => $values) {
            $targets[$productId] = ($count > 1 && $values !== [])
                ? ' ' . implode(' ', $values)
                : '';
        }

        return $targets;
    }
}
