<?php

namespace App\Modules\Catalog\Application\Actions\Attribute;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCategoryData;
use App\Modules\Catalog\Application\Interfaces\AttributeRepositoryInterface;

class ListAttributeByCategoryUseCase
{
    public function __construct(
        private readonly AttributeRepositoryInterface $attributeRepository,
    )
    {
    }

    /**
     * @return array{
     *     self: AttributeCategoryData[],
     *     parent: AttributeCategoryData[]
     * }
     */
    public function execute(int $id): array
    {
        return $this->attributeRepository->findForCategory($id);
    }
}
