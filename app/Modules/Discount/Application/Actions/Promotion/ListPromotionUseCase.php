<?php

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListEntityData;

readonly class ListPromotionUseCase
{

    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {

    }
    public function execute(): array
    {
        $promotions = $this->promotionRepository->getAll();

        return array_map(fn($promotion) => new ListEntityData(
            id: $promotion->id,
            name: $promotion->name,
            published: $promotion->status->isStarted(),
        ), $promotions);
    }
}
