<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Application\Services\ModificationValuesResolver;
use App\Modules\Catalog\Domain\Entities\ModificationEntity;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use InvalidArgumentException;

final readonly class AddProductToModificationUseCase
{
    public function __construct(
        private ProductRepositoryInterface      $productRepository,
        private ModificationRepositoryInterface $modificationRepository,
        private ModificationValuesResolver      $modificationValuesResolver,
    )
    {
    }

    public function execute(int $modificationId, int $productId, UserPermission $permission): ModificationEntity
    {
        if (!$permission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }
        if (!$this->productRepository->exists($productId)) {
            throw new InvalidArgumentException('Товар не найден ' . $productId);
        }
        $entity = $this->modificationRepository->getById($modificationId);
        $values = $this->modificationValuesResolver->forProduct(
            productId: $productId,
            attributes: $entity->attributes,
        );
        if ($entity->hasProductWithValues($values->toArray())) {
            throw new \DomainException('Товар с таким набором вариантов уже есть в модификации');
        }
        $entity->addProduct(
            productId: $productId,
            values: $values->toArray(),
        );

        return $this->modificationRepository->save($entity);
    }
}
