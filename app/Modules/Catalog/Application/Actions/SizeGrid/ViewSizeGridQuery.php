<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\SizeGrid;

use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewSizeGridQuery
{
    public function __construct(
        private SizeGridRepositoryInterface $sizeGridRepository,
    ) {
    }

    public function execute(int $id, UserPermission $userPermission): SizeGridEntity
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->sizeGridRepository->getById($id);
    }
}
