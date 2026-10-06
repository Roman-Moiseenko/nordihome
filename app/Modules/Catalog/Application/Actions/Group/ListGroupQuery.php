<?php

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Domain\Interfaces\GroupRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListNamePublishedData;

readonly class ListGroupQuery
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
    )
    {
    }
    public function execute(): array
    {
        $groups = $this->groupRepository->getAll();

        return array_map(fn($group) => new ListNamePublishedData(
            id: $group->id,
            name: $group->name,
            published: $group->published,
        ), $groups);
    }
}
