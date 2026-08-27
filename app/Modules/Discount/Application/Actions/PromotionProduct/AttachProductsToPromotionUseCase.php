<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\PromotionProduct;

use App\Modules\Discount\Application\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AttachProductsToPromotionUseCase
{
    public function __construct(
        private PromotionProductRepositoryInterface $promotionProductRepository,
    )
    {
    }

    /**
     * Добавить товары к акции (attach — дополняет существующие).
     *
     * @param int $promotionId
     * @param array<int, float> $products [product_id => price]
     * @param UserPermission $userPermission
     */
    public function execute(int $promotionId, array $products, UserPermission $userPermission): void
    {
        if (!$userPermission->can('discount.promotion.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->promotionProductRepository->attachProducts($promotionId, $products);
    }
}
