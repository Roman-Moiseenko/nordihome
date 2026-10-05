<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Domain\Interfaces\GroupProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\GroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveGroupUseCase
{
    public function __construct(
        private GroupRepositoryInterface        $groupRepository,
        private GroupProductRepositoryInterface $groupProductRepository,
    )
    {
    }

    public function execute(int $groupId, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

        $group = $this->groupRepository->getById($groupId);

        if ($group->isPublished()) {
            throw new \DomainException('Нельзя удалить опубликованную группу');
        }

        // Сначала отвязываем все товары, затем удаляем группу
        $this->groupProductRepository->detachAllProducts($groupId);
        $this->groupRepository->delete($groupId);
    }
}
