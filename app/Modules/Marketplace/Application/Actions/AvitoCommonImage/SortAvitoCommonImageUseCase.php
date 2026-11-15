<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoCommonImage;

use App\Modules\Marketplace\Application\DTOs\AvitoCommonImage\AvitoCommonImageSortData;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCommonImageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SortAvitoCommonImageUseCase
{
    public function __construct(
        private AvitoCommonImageRepositoryInterface $repository,
    ) {}

    public function execute(AvitoCommonImageSortData $dto, UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $ids = array_map('intval', $dto->ids);

        $this->repository->reorder($ids);
    }
}
