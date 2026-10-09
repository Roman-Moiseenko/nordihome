<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Catalog\Application\Actions\Modification\GetModificationByProductQuery;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateManagementProductData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Product;

readonly class SetManagementProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private GetModificationByProductQuery $getModificationByProductQuery,
    ) {
    }

    public function execute(int $id, UpdateManagementProductData $dto): ProductEntity
    {
        $applyToModification = $dto->modification ?? false;

        foreach ($this->resolveTargets($id, $applyToModification) as $targetId => $variantsLine) {
            $entity = $this->productRepository->getById($targetId);

            if ($dto->published) {
                $entity->publish();
            } else {
                $entity->unpublish();
            }
            $entity->notSale = (bool) $dto->notSale;
            $entity->priority = (bool) $dto->priority;
            $entity->priceReduced = (bool) $dto->priceReduced;
            $entity->onlyOnOrder = (bool) $dto->onlyOnOrder;
            $entity->hidePrice = (bool) $dto->hidePrice;
            $entity->preOrder = (bool) $dto->preOrder;

            $this->productRepository->save($entity);

            // Баланс и ячейки хранилищ — отдельные сущности (Balance, StorageItem),
            // работаем с моделью напрямую.
            $product = Product::findOrFail($targetId);

            if (is_array($dto->balance)) {
                $product->balance->min = (int) ($dto->balance['min'] ?? 0);
                $product->balance->max = $dto->balance['max'] ?? null;
                $product->balance->buy = (bool) ($dto->balance['buy'] ?? false);
                $product->push();
            }

            if (is_array($dto->storages)) {
                foreach ($dto->storages as $item) {
                    if (!isset($item['id'])) {
                        continue;
                    }

                    $storageItem = StorageItem::find($item['id']);
                    if ($storageItem !== null) {
                        $storageItem->cell = (string) ($item['cell'] ?? '');
                        $storageItem->save();
                    }
                }
            }
        }

        return $this->productRepository->getById($id);
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
