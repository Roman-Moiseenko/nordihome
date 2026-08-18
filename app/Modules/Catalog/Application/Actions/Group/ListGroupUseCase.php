<?php

namespace App\Modules\Catalog\Application\Actions\Group;

use App\Modules\Catalog\Entity\Group;

class ListGroupUseCase
{
    public function execute(): array
    {
        $groups = Group::orderBy('name')->getModels();

        return array_map(fn($group) => [
            'id' => $group->id,
            'name' => $group->name,
            'published' => $group->published,
        ], $groups);
    }
}
