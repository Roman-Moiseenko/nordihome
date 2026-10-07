<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationCreateData;
use App\Modules\Catalog\Application\Services\ModificationValuesResolver;
use App\Modules\Catalog\Domain\Entities\ModificationEntity;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use InvalidArgumentException;

final readonly class CreateModificationUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private TransactionManagerInterface $transactionManager,
        private ModificationRepositoryInterface $modificationRepository,
        private ModificationValuesResolver $modificationValuesResolver,
    ) {
    }

    public function execute(ModificationCreateData $dto, UserPermission $permission): ModificationEntity
    {
        if (!$permission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        if (!$this->productRepository->exists($dto->productId)) {
            throw new InvalidArgumentException('Товар не найден ' . $dto->productId);
        }

        return $this->transactionManager->execute(function () use ($dto): ModificationEntity {
            $entity = ModificationEntity::create(
                name: $dto->name,
                attributes: $dto->attributes,
            );

            // Разрешаем значения первого товара по осям модификации.
            // Результат — ModificationValues{attribute_id: variant_id}.
            $values = $this->modificationValuesResolver->forProduct(
                productId: $dto->productId,
                attributes: $dto->attributes,
            );

            // Первый товар автоматически становится primary.
            $entity->addProduct(
                productId: $dto->productId,
                values: $values->toArray(),
                primary: true,
            );

            return $this->modificationRepository->save($entity);
        });
    }
}
