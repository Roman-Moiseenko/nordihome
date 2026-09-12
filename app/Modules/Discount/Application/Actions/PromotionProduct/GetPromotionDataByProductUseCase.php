<?php

namespace App\Modules\Discount\Application\Actions\PromotionProduct;

use App\Modules\Discount\Application\DTOs\PromotionProduct\PromotionProductCartData;
use App\Modules\Discount\Domain\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Discount\Domain\Interfaces\PromotionRepositoryInterface;

readonly class GetPromotionDataByProductUseCase
{

    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
        private PromotionProductRepositoryInterface $promotionProductRepository,
    ){}
    public function execute(int $productId):? PromotionProductCartData
    {
        $promotionProduct = $this->promotionProductRepository->getPromotionId($productId);
        if (is_null($promotionProduct)) return null;

        $promotion = $this->promotionRepository->getById($promotionProduct->promotionId);

        if (!$promotion->status->isStarted()) return null;

        return new PromotionProductCartData(
            id: $promotionProduct->promotionId,
            name: $promotion->name,
            price: $promotionProduct->price,
        );
    }
}
