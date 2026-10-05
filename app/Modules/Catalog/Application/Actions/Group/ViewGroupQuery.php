<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Domain\Entities\GroupEntity;
use App\Modules\Catalog\Domain\Interfaces\GroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewGroupQuery
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): GroupEntity
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->groupRepository->getById($id);
    }
}
