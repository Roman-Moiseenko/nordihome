<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class RemovePromotionUseCase
{
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
    )
    {
    }

    /**
     * Удалить акцию по ID.
     */
    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('discount.promotion.delete')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->promotionRepository->delete($id);
    }
}
