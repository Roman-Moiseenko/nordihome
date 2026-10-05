<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Application\DTOs\Group\GroupUpdateData;
use App\Modules\Catalog\Domain\Entities\GroupEntity;
use App\Modules\Catalog\Domain\Interfaces\GroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class UpdateGroupUseCase
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
    )
    {
    }

    public function execute(int $groupId, GroupUpdateData $dto, UserPermission $userPermission): GroupEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $group = $this->groupRepository->getById($groupId);

        $group->name = $dto->name;

        // Slug: если пустой — генерируем из названия
        $slugValue = $dto->slug;
        $slugString = $slugValue !== null ? trim($slugValue) : '';
        if ($slugString === '') {
            $slugString = Str::slug($group->name);
        }

        $slug = new Slug($slugString);
        if ($this->groupRepository->existsSlug((string) $slug, $groupId)) {
            $slug = new Slug((string) $slug . '-' . uniqid());
        }
        $group->slug = $slug;

        if ($dto->description !== null) {
            $group->description = $dto->description;
        }

        if ($dto->published !== null) {
            $dto->published ? $group->publish() : $group->unpublish();
        }

        return $this->groupRepository->save($group);
    }
}
