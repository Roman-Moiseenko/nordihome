<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\Actions\Modification\GetModificationByProductQuery;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateCompositeProductData;
use App\Modules\Catalog\Infrastructure\Models\Product;

readonly class SetCompositeProductUseCase
{
    public function __construct(
        private GetModificationByProductQuery $getModificationByProductQuery,
    ) {
    }

    public function execute(int $id, UpdateCompositeProductData $dto): void
    {
        $applyToModification = $dto->modification ?? false;

        foreach ($this->resolveTargets($id, $applyToModification) as $targetId => $variantsLine) {
            $product = Product::findOrFail($targetId);

            if ($dto->action === 'remove' && $dto->productId !== null) {
                $product->composites()->detach($dto->productId);
                continue;
            }

            if ($dto->action === 'edit' && $dto->composite !== null) {
                foreach ($dto->composite as $item) {
                    if (isset($item['id'])) {
                        $product->composites()->updateExistingPivot($item['id'], [
                            'quantity' => (int) ($item['quantity'] ?? 1),
                        ]);
                    }
                }
                continue;
            }

            if ($dto->productId !== null && !$product->isComposite($dto->productId)) {
                $product->composites()->attach($dto->productId, [
                    'quantity' => (int) ($dto->quantity ?? 1),
                ]);
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
