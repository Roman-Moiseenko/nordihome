<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product;

use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

/**
 * Массовое действие над товарами (черновик, публикация, продажа, удаление).
 */
readonly class MassActionProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * @param int[] $ids
     */
    public function execute(string $action, array $ids, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        if (empty($ids)) {
            throw new \DomainException('Не выбраны товары');
        }

        if (empty($action)) {
            throw new \DomainException('Не выбрано действие');
        }

        foreach ($ids as $id) {
            $id = (int) $id;
            $product = $this->productRepository->getById($id);

            if ($action === 'draft' && $product->isPublished()) {
                $product->unpublish();
                $this->productRepository->save($product);
            } elseif ($action === 'published' && !$product->isPublished()) {
                $product->publish();
                $this->productRepository->save($product);
            } elseif ($action === 'not_sale' && !$product->notSale) {
                $product->notSale = true;
                $this->productRepository->save($product);
            } elseif ($action === 'to_sale' && $product->notSale) {
                $product->notSale = false;
                $this->productRepository->save($product);
            } elseif ($action === 'remove') {
                $this->productRepository->delete($id);
            }
        }
    }
}
