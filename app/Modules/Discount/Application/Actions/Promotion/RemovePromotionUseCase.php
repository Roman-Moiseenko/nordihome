<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Domain\Interfaces\PromotionRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

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
    public function execute(int $id, UserPermission $permission): void
    {
        if (!$permission->can('discount.promotion.delete')) throw new AccessDeniedException();

        //TODO Сделать проверку есть ли OrderItem с текущей акцией

        $this->promotionRepository->delete($id);
    }
}
