<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductValueViewData;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductValueRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ListAvitoProductValuesQuery
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private AvitoProductValueRepositoryInterface $valueRepository,
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    /**
     * @return AvitoProductValueViewData[]
     */
    public function execute(int $avitoProductId, UserPermission $permission): array
    {
        if (!$permission->can('marketplace.avito.view')) {
            throw new AccessDeniedException();
        }

        $product = $this->avitoProductRepository->getById($avitoProductId);

        if ($product->avitoCategoryId === null) {
            return [];
        }

        $fields = [];
        foreach ($this->fieldRepository->getByCategoryId($product->avitoCategoryId) as $field) {
            $fields[$field['id']] = $field;
        }

        $result = [];
        foreach ($this->valueRepository->getByProductId($avitoProductId) as $value) {
            $field = $fields[$value->avitoCategoryFieldId] ?? null;
            if ($field === null) {
                continue;
            }

            $result[] = AvitoProductValueViewData::fromEntity($value, $field);
        }

        return $result;
    }
}
