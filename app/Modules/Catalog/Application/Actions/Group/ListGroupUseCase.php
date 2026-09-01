<?php

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Shared\Application\DTOs\ListEntityData;

class ListGroupUseCase
{
    public function execute(): array
    {
        $groups = Group::orderBy('name')->getModels();

        return array_map(fn($group) => new ListEntityData(
            id: $group->id,
            name: $group->name,
            published: $group->published,
        ), $groups);
    }
}
