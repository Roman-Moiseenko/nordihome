<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Application\DTOs\Group\GroupCreateData;
use App\Modules\Catalog\Domain\Entities\GroupEntity;
use App\Modules\Catalog\Domain\Interfaces\GroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateGroupUseCase
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
    ) {}

    public function execute(GroupCreateData $dto, UserPermission $userPermission): GroupEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        $slug = new Slug($dto->name);

        if ($this->groupRepository->existsSlug((string) $slug)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }

        $group = new GroupEntity(
            name: $dto->name,
            slug: $slug,
        );

        return $this->groupRepository->save($group);
    }
}
