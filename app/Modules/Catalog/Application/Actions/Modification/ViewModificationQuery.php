<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationViewData;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

final readonly class ViewModificationQuery
{
    public function __construct(
        private ModificationRepositoryInterface $modificationRepository,
    ) {
    }

    /**
     * Данные карточки модификации: название, оси с вариантами и товары.
     */
    public function execute(int $id, UserPermission $permission): ModificationViewData
    {
        if (!$permission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->modificationRepository->getViewData($id);
    }
}
