<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

class ViewTagUseCase
{
    public function __construct(
        private readonly TagRepositoryInterface $repository,
    )
    {}

    public function execute(int $tagId, UserPermission $userPermission): TagEntity
    {
        if (!$userPermission->can('catalog.product.view')) throw new \DomainException('Доступ запрещён');

        return $this->repository->getById($tagId);

    }
}
