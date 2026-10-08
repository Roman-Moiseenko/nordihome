<?php

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateCommonProductData;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

readonly class SetCommonProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    )
    {

    }

    public function execute(int $id, UpdateCommonProductData $dto)
    {
        $productEntity = $this->productRepository->getById($id);




        $this->productRepository->save($productEntity);
    }
}
