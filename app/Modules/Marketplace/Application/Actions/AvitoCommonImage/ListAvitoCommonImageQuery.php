<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoCommonImage;

use App\Modules\Marketplace\Domain\Entities\AvitoCommonImageEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCommonImageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ListAvitoCommonImageQuery
{
    public function __construct(
        private AvitoCommonImageRepositoryInterface $repository,
    ) {}

    /**
     * @return AvitoCommonImageEntity[]
     */
    public function execute(UserPermission $permission): array
    {
        if (!$permission->can('marketplace.avito.view')) {
            throw new AccessDeniedException();
        }

        return $this->repository->getAll();
    }
}
