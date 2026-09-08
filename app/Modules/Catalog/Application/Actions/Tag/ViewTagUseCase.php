<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

class ViewTagUseCase
{
    public function __construct(
        private readonly TagRepositoryInterface $repository,
    )
    {}

    public function execute(int $tagId, UserPermission $userPermission): TagEntity
    {
        if (!$userPermission->can('catalog.product.view')) throw new AccessDeniedException();

        return $this->repository->getById($tagId);

    }
}
