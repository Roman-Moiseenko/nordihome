<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Base\Entity\Dimensions;
use App\Modules\Catalog\Application\DTOs\Product\ProductUpdateData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class UpdateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    )
    {
    }

    public function execute(ProductUpdateData $dto, UserPermission $userPermission): ProductEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $product = $this->productRepository->getById($dto->id);

        // ======================== Основные поля ========================
        if ($dto->name !== null) {
            $product->name = $dto->name;
        }

        if ($dto->namePrint !== null) {
            $product->namePrint = $dto->namePrint;
        }

        if ($dto->code !== null) {
            $product->code = new Code($dto->code);
        }

        if ($dto->slug !== null) {
            $product->slug = new Slug($dto->slug);
        }

        // ======================== Текстовые поля ========================
        if ($dto->description !== null) {
            $product->description = $dto->description;
        }

        if ($dto->short !== null) {
            $product->short = $dto->short;
        }

        if ($dto->care !== null) {
            $product->care = $dto->care;
        }

        // ======================== Числовые поля ========================
        if ($dto->mainCategoryId !== null) {
            $product->mainCategoryId = $dto->mainCategoryId;
        }

        if ($dto->brandId !== null) {
            $product->brandId = $dto->brandId;
        }

        // ======================== Булевы флаги ========================
        if ($dto->published !== null) {
            if ($dto->published) {
                $product->publish();
            }
        }

        // ======================== Габариты ========================
        if ($dto->dimensions !== null) {
            $product->dimensions = Dimensions::fromArray($dto->dimensions);
        }

        return $this->productRepository->save($product);
    }
}
