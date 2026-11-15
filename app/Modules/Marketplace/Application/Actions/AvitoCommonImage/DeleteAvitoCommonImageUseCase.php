<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoCommonImage;

use App\Modules\Marketplace\Domain\Interfaces\AvitoCommonImageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class DeleteAvitoCommonImageUseCase
{
    public function __construct(
        private AvitoCommonImageRepositoryInterface $repository,
    ) {}

    public function execute(int $id, UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $this->repository->delete($id);
    }
}
