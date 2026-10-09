<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

final readonly class RemoveModificationUseCase
{
    public function __construct(

        private ModificationRepositoryInterface $modificationRepository,
    ) {
    }

    public function execute(int $modificationId, UserPermission $permission): void
    {
        if (!$permission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

            $this->modificationRepository->delete($modificationId);

    }
}
