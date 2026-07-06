<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Application\DTOs\Product\ParserProductCategoryData;
use App\Modules\Parser\Application\DTOs\Product\ParserProductFilterData;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexParserProductUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $productRepository,
    ) {}


    public function execute(ParserProductFilterData $filter, UserPermission $userPermission): LengthAwarePaginator
    {
        if (!$userPermission->can('parser.product.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        $products = $this->productRepository->getFilteredPaginated($filter);

        $products->through(
            fn($product) => ParserProductCategoryData::fromEntity($product)
        );
        return $products;
    }
}
