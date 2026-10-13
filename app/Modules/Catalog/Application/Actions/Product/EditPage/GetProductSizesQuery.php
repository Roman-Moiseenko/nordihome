<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewProductSizesData;
use App\Modules\Catalog\Domain\Entities\ProductSizeEntity;
use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Entities\SizeGridValueEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductSizeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;

final readonly class GetProductSizesQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private SizeGridRepositoryInterface $sizeGridRepository,
        private ProductSizeRepositoryInterface $productSizeRepository,
    ) {
    }

    public function execute(int $id): ViewProductSizesData
    {
        $product = $this->productRepository->getById($id);

        $sizeGrids = array_map(
            fn(SizeGridEntity $grid) => [
                'id' => $grid->id,
                'name' => $grid->name,
            ],
            $this->sizeGridRepository->getAll(),
        );

        $sizeGrid = null;
        $productSizes = [];

        if ($product->sizeGridId !== null) {
            $grid = $this->sizeGridRepository->getById($product->sizeGridId);

            $sizeGrid = [
                'id' => $grid->id,
                'name' => $grid->name,
                'dimensions' => array_map(
                    fn(SizeGridDimension $dimension) => [
                        'code' => $dimension->code,
                        'name' => $dimension->name,
                    ],
                    $grid->dimensions,
                ),
                'values' => array_values(array_map(
                    fn(SizeGridValueEntity $value) => [
                        'dimensionCode' => $value->dimensionCode,
                        'value' => $value->value,
                        'label' => $value->label,
                        'sortOrder' => $value->sortOrder,
                    ],
                    array_filter($grid->values, fn(SizeGridValueEntity $v) => $v->isActive),
                )),
            ];

            $productSizes = array_map(
                fn(ProductSizeEntity $size) => [
                    'id' => $size->id,
                    'values' => $size->values,
                    'isActive' => $size->isActive,
                ],
                $this->productSizeRepository->getByProductId($id),
            );
        }

        return new ViewProductSizesData(
            id: $id,
            sizeGridId: $product->sizeGridId,
            sizeGrids: $sizeGrids,
            sizeGrid: $sizeGrid,
            productSizes: $productSizes,
        );
    }
}
