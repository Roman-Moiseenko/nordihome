<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Application\DTOs\Product\ParserProductCategoryData;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use Illuminate\Pagination\LengthAwarePaginator;

class ListAllProductByCategoryUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $productRepository,
    )
    {
    }

    public function execute(int $id, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $paginator = $this->productRepository->findAllByCategoryId($id, $perPage, $page);

        // Заменяем коллекцию сущностей на коллекцию DTO
        $dto = $paginator->getCollection()->map(
            fn(ParserProductEntity $product) => ParserProductCategoryData::fromEntity($product)
        );

        $paginator->setCollection($dto);

        return $paginator;
    }
}
