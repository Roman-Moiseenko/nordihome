<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Domain\Entities\ModificationEntity;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

final readonly class SetPrimaryModificationProductUseCase
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private ModificationRepositoryInterface $modificationRepository,
    ) {
    }

    public function execute(int $modificationId, int $productId, UserPermission $permission): ModificationEntity
    {
        if (!$permission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        return $this->transactionManager->execute(function () use ($modificationId, $productId): ModificationEntity {
            $entity = $this->modificationRepository->getById($modificationId);

            $entity->setPrimary($productId);

            return $this->modificationRepository->save($entity);
        });
    }
}
