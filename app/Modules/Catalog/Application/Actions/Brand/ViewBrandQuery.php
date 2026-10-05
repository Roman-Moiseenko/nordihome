<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Brand;

use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewBrandQuery
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): BrandEntity
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->brandRepository->getById($id);
    }
}
