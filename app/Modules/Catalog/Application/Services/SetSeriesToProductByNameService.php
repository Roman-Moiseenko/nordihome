<?php

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Application\Actions\Series\FindOrCreateSeriesUseCase;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

readonly class SetSeriesToProductByNameService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private FindOrCreateSeriesUseCase $findOrCreateSeriesUseCase,
    )
    {

    }

    public function execute(int $productId, string $seriesName): ProductEntity
    {
        $series = $this->findOrCreateSeriesUseCase->execute($seriesName);
        $product = $this->productRepository->getById($productId);
        $product->seriesId = $series->id;
        return $this->productRepository->save($product);
    }
}
