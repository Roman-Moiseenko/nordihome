<?php

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\Entities\PromotionEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewPromotionUseCase
{
    public function __construct(
        private PromotionRepositoryInterface $repository
    ){
    }

    public function execute(int $id, UserPermission $userPermission): PromotionEntity
    {
        // Проверка прав доступа
        if (!$userPermission->can('discount.promotion.view')) throw new AccessDeniedException();

        return $this->repository->getById($id);
    }
}
