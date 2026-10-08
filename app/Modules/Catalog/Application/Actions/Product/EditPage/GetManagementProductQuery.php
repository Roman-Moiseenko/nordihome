<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewManagementProductData;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Product;

final readonly class GetManagementProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function execute(int $id): ViewManagementProductData
    {
        $entity = $this->productRepository->getById($id);

        // Ячейки хранилищ и баланс не входят в ProductEntity — читаем модель напрямую.
        $product = Product::with(['storageItems.storage', 'balance'])->findOrFail($id);

        $storages = $product->storageItems
            ->map(fn(StorageItem $item) => [
                'id' => $item->id,
                'name' => $item->storage->name ?? '',
                'cell' => $item->cell ?? '',
            ])
            ->values()
            ->toArray();

        $balance = [
            'min' => (int) ($product->balance?->min ?? 0),
            'max' => $product->balance?->max ?? null,
            'buy' => (bool) ($product->balance?->buy ?? false),
        ];

        return ViewManagementProductData::fromEntity($entity, $storages, $balance);
    }
}
