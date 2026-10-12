<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\SizeGrid;

use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveSizeGridUseCase
{
    public function __construct(
        private SizeGridRepositoryInterface $sizeGridRepository,
    ) {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

        $this->sizeGridRepository->delete($id);
    }
}
