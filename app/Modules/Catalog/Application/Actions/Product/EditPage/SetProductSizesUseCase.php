<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateProductSizesData;
use App\Modules\Catalog\Domain\Entities\ProductSizeEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductSizeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;

final readonly class SetProductSizesUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private SizeGridRepositoryInterface $sizeGridRepository,
        private ProductSizeRepositoryInterface $productSizeRepository,
    ) {
    }

    public function execute(UpdateProductSizesData $dto): void
    {
        $product = $this->productRepository->getById($dto->id);

        if ($dto->sizeGridId === null) {
            $this->productSizeRepository->clearForProduct($dto->id);

            $product->sizeGridId = null;
            $this->productRepository->save($product);

            return;
        }

        $grid = $this->sizeGridRepository->getById($dto->sizeGridId);

        $product->sizeGridId = $dto->sizeGridId;
        $this->productRepository->save($product);

        $desiredActive = [];
        foreach (($dto->sizes ?? []) as $size) {
            if (!is_array($size)) {
                continue;
            }

            $values = $size['values'] ?? [];
            if (!is_array($values) || $values === []) {
                continue;
            }

            $desiredActive[ProductSizeEntity::keyFromValues($values)] = (bool) ($size['isActive'] ?? true);
        }

        $this->productSizeRepository->rebuildForProduct($dto->id, $grid, $desiredActive);
    }
}
