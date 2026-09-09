<?php

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Shared\Application\DTOs\ListNamePublishedData;

class ListGroupUseCase
{
    public function execute(): array
    {
        $groups = Group::orderBy('name')->getModels();

        return array_map(fn($group) => new ListNamePublishedData(
            id: $group->id,
            name: $group->name,
            published: $group->published,
        ), $groups);
    }
}
