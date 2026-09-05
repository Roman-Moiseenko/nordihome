<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Application\DTOs\Product\ProductFastCreateData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class FastCreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    )
    {
    }

    /**
     * @throws \DomainException
     */
    public function execute(ProductFastCreateData $dto, UserPermission $userPermission): ProductEntity
    {
        if (!$userPermission->can('catalog.product.create')) throw new AccessDeniedException();

        // Проверка уникальности артикула
        $existing = $this->productRepository->findByCode($dto->code);
        if ($existing !== null)
            throw new \DomainException('Товар с артикулом ' . $dto->code . ' уже существует');

        $slug = $dto->slug !== null
            ? new Slug($dto->slug)
            : new Slug($dto->name);
        $product = new ProductEntity(
            name: $dto->name,
            code: new Code($dto->code),
            slug: $slug,
            mainCategoryId: $dto->categoryId,
            brandId: $dto->brandId,
        );

        return $this->productRepository->save($product);
    }
}
