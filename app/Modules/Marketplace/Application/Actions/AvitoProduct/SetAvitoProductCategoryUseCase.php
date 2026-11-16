<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductCategoryData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Entities\AvitoProductValueEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductValueRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Support\Facades\DB;

readonly class SetAvitoProductCategoryUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private AvitoProductValueRepositoryInterface $valueRepository,
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    public function execute(int $id, AvitoProductCategoryData $dto, UserPermission $permission): AvitoProductEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $product = $this->avitoProductRepository->getById($id);
        $product->avitoCategoryId = $dto->avitoCategoryId;

        DB::transaction(function () use ($product) {
            $this->avitoProductRepository->save($product);

            // При смене категории удаляем старые значения и создаём новые (пустые).
            $this->valueRepository->deleteByProductId($product->id);

            foreach ($this->fieldRepository->getByCategoryId($product->avitoCategoryId) as $field) {
                $this->valueRepository->save(new AvitoProductValueEntity(
                    avitoProductId: $product->id,
                    avitoCategoryFieldId: $field['id'],
                ));
            }
        });

        return $product;
    }
}
