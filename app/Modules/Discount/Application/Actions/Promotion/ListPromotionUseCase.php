<?php

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Domain\Interfaces\PromotionRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListNamePublishedData;

readonly class ListPromotionUseCase
{

    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {

    }
    public function execute(): array
    {
        $promotions = $this->promotionRepository->getAll();

        return array_map(fn($promotion) => new ListNamePublishedData(
            id: $promotion->id,
            name: $promotion->name,
            published: $promotion->status->isStarted(),
        ), $promotions);
    }
}
