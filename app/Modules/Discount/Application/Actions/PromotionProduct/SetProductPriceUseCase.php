<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\PromotionProduct;

use App\Modules\Discount\Application\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class SetProductPriceUseCase
{
    public function __construct(
        private PromotionProductRepositoryInterface $promotionProductRepository,
    )
    {
    }

    /**
     * Установить цену товара в акции.
     *
     * @param int $promotionId
     * @param int $productId
     * @param float $price
     * @param UserPermission $userPermission
     */
    public function execute(int $promotionId, int $productId, float $price, UserPermission $userPermission): void
    {
        if (!$userPermission->can('discount.promotion.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->promotionProductRepository->setPrice($promotionId, $productId, $price);
    }
}
