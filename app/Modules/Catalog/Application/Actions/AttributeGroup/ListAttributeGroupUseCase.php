<?php

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;

readonly class ListAttributeGroupUseCase
{
    public function __construct(
        private AttributeGroupRepositoryInterface $repository,
    )
    {
    }

    /**
     * @return array{id: int, name: string, parser: ?string}[]
     */
    public function execute(): array
    {
        $groups = $this->repository->getAll();

        return array_map(fn($group) => [
            'id' => $group->id,
            'name' => $group->name,
        ], $groups);
    }
}
