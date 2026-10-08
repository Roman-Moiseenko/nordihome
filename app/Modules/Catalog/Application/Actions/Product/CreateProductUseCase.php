<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\ProductCreateData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

/**
 * Создание товара (Catalog/Product/Create).
 */
readonly class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(ProductCreateData $dto, UserPermission $userPermission): ProductEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        if ($this->productRepository->findByCode($dto->code) !== null) {
            throw new \DomainException('Товар с артикулом ' . $dto->code . ' уже существует');
        }

        $slug = ($dto->slug !== null && $dto->slug !== '')
            ? new Slug($dto->slug)
            : new Slug($dto->name);

        $product = new ProductEntity(
            name: $dto->name,
            code: new Code($dto->code),
            slug: $slug,
            mainCategoryId: $dto->categoryId,
            brandId: $dto->brandId,
        );

        $product->namePrint = $dto->namePrint;
        $product->comment = $dto->comment ?? '';
        $product->countryId = $dto->countryId;
        $product->measuringId = $dto->measuringId;
        $product->markingTypeId = $dto->markingTypeId;
        $product->fractional = $dto->fractional ?? false;

        return $this->productRepository->save($product);
    }
}
